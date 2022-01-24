<?php

namespace Tests\Feature;

use App\Http\Livewire\CountryStatisticsLivewire;
use App\Models\CountryStatistics;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CountryStatisticsTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function test_dashboard_page_has_country_statistics_livewire_component()
	{
		$user = User::factory()->create();
		$this->actingAs($user)->get(route('dashboard.country'))
			->assertSeeLivewire('country-statistics-livewire')
			->assertSuccessful()
			->assertStatus(200);
	}

	/** @test */
	public function test_country_statistics_search_functionality()
	{
		$user = User::factory()->create();

		$countryOne = CountryStatistics::factory()->create([
			'name' => 'Afghanistan',
		]);

		$countryTwo = CountryStatistics::factory()->create([
			'name' => 'Russia',
		]);

		$countryThree = CountryStatistics::factory()->create([
			'name' => 'Georgia',
		]);

		$countryFour = CountryStatistics::factory()->create([
			'name' => 'Albania',
		]);

		Livewire::test(CountryStatisticsLivewire::class)
			->set('search', 'Afgha')
			->assertSee('Afghanistan')
			->assertDontSee('Russia');
	}

	/** @test */
	public function test_country_statistics_search_functionality_nothing_found()
	{
		$user = User::factory()->create();

		$countryOne = CountryStatistics::factory()->create([
			'name' => 'Afghanistan',
		]);

		$countryTwo = CountryStatistics::factory()->create([
			'name' => 'Russia',
		]);

		$countryThree = CountryStatistics::factory()->create([
			'name' => 'Georgia',
		]);

		$countryFour = CountryStatistics::factory()->create([
			'name' => 'Albania',
		]);

		Livewire::test(CountryStatisticsLivewire::class)
			->set('search', 'dxasdxasxdsa')
			->assertDontSee('Afghanistan')
			->assertSee('Nothing found for this query');
	}
}
