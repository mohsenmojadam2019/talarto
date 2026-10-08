<?php
namespace App\Services;

use App\Models\{CalendarDate, CalendarHold, CalendarOccupancy, Reservation};
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AvailabilityService
{
    public function cleanupExpired(): void
    {
        CalendarHold::where('expires_at', '<=', now())->delete();
    }

    public function available(CarbonInterface $date, string $slot, ?int $ignoreReservationId = null): bool
    {
        $this->cleanupExpired();
        if (CalendarDate::whereDate('date', $date)->whereIn('status', ['booked', 'unavailable'])->exists()) {
            return false;
        }
        $occupied = CalendarOccupancy::whereDate('date', $date)->where('time_slot', $slot);
        if ($ignoreReservationId) $occupied->where('reservation_id', '!=', $ignoreReservationId);
        if ($occupied->exists()) return false;
        // Compatibility with historical reservations not yet migrated into occupancies.
        $confirmed = Reservation::whereDate('event_date', $date)
            ->where('time_slot', $slot)->whereIn('status', ['confirmed', 'done']);
        if ($ignoreReservationId) $confirmed->whereKeyNot($ignoreReservationId);
        if ($confirmed->exists()) return false;
        $holds = CalendarHold::whereDate('date', $date)->where('time_slot', $slot)
            ->where('expires_at', '>', now());
        if ($ignoreReservationId) {
            $holds->where(fn ($q) => $q->whereNull('reservation_id')->orWhere('reservation_id', '!=', $ignoreReservationId));
        }
        return !$holds->exists();
    }

    public function placeHold(int $userId, int $reservationId, CarbonInterface $date, string $slot, int $minutes = 15): void
    {
        DB::transaction(function () use ($userId, $reservationId, $date, $slot, $minutes) {
            $this->cleanupExpired();
            if (!$this->available($date, $slot, $reservationId)) $this->unavailable();
            CalendarHold::where('reservation_id', $reservationId)->delete();
            try {
                CalendarHold::create([
                    'user_id' => $userId, 'reservation_id' => $reservationId,
                    'date' => $date->toDateString(), 'time_slot' => $slot,
                    'expires_at' => now()->addMinutes($minutes),
                ]);
            } catch (QueryException $e) {
                $this->unavailable();
            }
        });
    }

    // Unique index is the final guarantee even when simultaneous requests pass availability checks.
    public function confirm(Reservation $reservation): void
    {
        try {
            DB::transaction(function () use ($reservation) {
                $reservation = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
                if ($reservation->status === 'confirmed') return;
                if (!in_array($reservation->status, ['new', 'contacted'], true)) {
                    throw ValidationException::withMessages(['reservation' => 'وضعیت این رزرو برای تأیید معتبر نیست.']);
                }
                if (!$this->available($reservation->event_date, $reservation->time_slot, $reservation->id)) $this->unavailable();
                CalendarOccupancy::create([
                    'reservation_id' => $reservation->id,
                    'date' => $reservation->event_date->toDateString(),
                    'time_slot' => $reservation->time_slot,
                ]);
                $reservation->update(['status' => 'confirmed', 'confirmed_at' => now(), 'hold_expires_at' => null]);
                $this->release($reservation->id);
            }, 3);
        } catch (QueryException $e) {
            throw ValidationException::withMessages(['reservation' => 'این تاریخ و سانس قبلاً رزرو قطعی شده است.']);
        }
    }

    public function release(?int $reservationId): void
    {
        if ($reservationId) CalendarHold::where('reservation_id', $reservationId)->delete();
    }

    private function unavailable(): never
    {
        throw ValidationException::withMessages(['event_date_jalali' => 'این تاریخ و سانس دیگر در دسترس نیست.']);
    }
}
