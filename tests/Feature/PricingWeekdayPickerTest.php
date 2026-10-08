<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\PricingRule;
use Tests\TestCase;

class PricingWeekdayPickerTest extends TestCase
{
    private function adminSession(): array
    {
        $user = AdminUser::create([
            'name' => 'Weekday QA',
            'email' => 'weekday-qa@example.test',
            'password' => 'long-safe-testing-password',
            'active' => true,
        ]);
        return ['admin_user_id' => $user->id];
    }

    public function test_new_rule_saves_multiple_selected_days(): void
    {
        $session = $this->adminSession();
        $this->withSession($session)->post(route('admin.commerce.rules.store'), [
            'title' => 'قانون پایان هفته',
            'direction' => 'increase',
            'rule_type' => 'percentage',
            'amount' => 15,
            'weekdays' => [4, 5],
            'priority' => 100,
            'active' => '1',
        ])->assertSessionHasNoErrors();

        $rule = PricingRule::where('title', 'قانون پایان هفته')->firstOrFail();
        $this->assertEquals([4, 5], $rule->weekdays);

        $response = $this->withSession($session)->get(route('admin.commerce'));
        $response->assertOk()
            ->assertSee('weekday-picks-grid', false)
            ->assertSee('روزهای اعمال قانون')
            ->assertSee('name="weekdays[]"', false)
            ->assertDontSee("stack('head')@", false);
    }

    public function test_selected_weekdays_can_be_updated(): void
    {
        $session = $this->adminSession();
        $rule = PricingRule::create([
            'title' => 'روزهای خاص',
            'direction' => 'increase',
            'rule_type' => 'percentage',
            'amount' => 10,
            'priority' => 50,
            'weekdays' => [4, 5],
            'active' => true,
        ]);
        $this->withSession($session)->put(route('admin.commerce.rules.update', $rule), [
            'title' => 'روزهای خاص',
            'direction' => 'increase',
            'rule_type' => 'percentage',
            'amount' => 10,
            'priority' => 50,
            'weekdays' => [0, 3, 6],
            'active' => '1',
        ])->assertSessionHasNoErrors();
        $this->assertEquals([0, 3, 6], $rule->fresh()->weekdays);
    }
}
