<?php

namespace Tests\Feature\Database;

use App\Models\BoardingHouse;
use App\Models\Bonus;
use App\Models\Category;
use App\Models\City;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Testimonial;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::disk('public')->deleteDirectory('seeders/ngekos');
    }

    public function test_database_seeder_builds_a_repeatable_relational_dataset_with_copied_images(): void
    {
        $this->seed();

        $this->assertGreaterThanOrEqual(4, City::count());
        $this->assertGreaterThanOrEqual(5, Category::count());
        $this->assertGreaterThanOrEqual(6, BoardingHouse::count());
        $this->assertGreaterThanOrEqual(3, Room::count());
        $this->assertGreaterThanOrEqual(3, RoomImage::count());
        $this->assertGreaterThanOrEqual(3, Bonus::count());
        $this->assertGreaterThanOrEqual(1, Testimonial::count());
        $this->assertGreaterThanOrEqual(1, Transaction::count());

        $boardingHouse = BoardingHouse::query()
            ->with(['city', 'category', 'rooms.images', 'bonuses', 'testimonial', 'transactions'])
            ->firstOrFail();

        $this->assertNotNull($boardingHouse->city);
        $this->assertNotNull($boardingHouse->category);
        $this->assertNotEmpty($boardingHouse->rooms);
        $this->assertNotEmpty($boardingHouse->bonuses);
        $this->assertNotEmpty($boardingHouse->testimonial);
        $this->assertNotEmpty($boardingHouse->transactions);

        $this->assertTrue(Storage::disk('public')->exists($boardingHouse->city->image));
        $this->assertTrue(Storage::disk('public')->exists($boardingHouse->category->image));
        $this->assertTrue(Storage::disk('public')->exists($boardingHouse->thumbnail));
        $this->assertTrue(Storage::disk('public')->exists($boardingHouse->rooms->first()->images->first()->image));
        $this->assertTrue(Storage::disk('public')->exists($boardingHouse->bonuses->first()->image));
        $this->assertTrue(Storage::disk('public')->exists($boardingHouse->testimonial->first()->photo));

        $countsBeforeReseed = [
            'cities' => City::count(),
            'categories' => Category::count(),
            'boarding_houses' => BoardingHouse::count(),
            'rooms' => Room::count(),
            'room_images' => RoomImage::count(),
            'bonuses' => Bonus::count(),
            'testimonials' => Testimonial::count(),
            'transactions' => Transaction::count(),
        ];

        $this->seed();

        $this->assertSame($countsBeforeReseed['cities'], City::count());
        $this->assertSame($countsBeforeReseed['categories'], Category::count());
        $this->assertSame($countsBeforeReseed['boarding_houses'], BoardingHouse::count());
        $this->assertSame($countsBeforeReseed['rooms'], Room::count());
        $this->assertSame($countsBeforeReseed['room_images'], RoomImage::count());
        $this->assertSame($countsBeforeReseed['bonuses'], Bonus::count());
        $this->assertSame($countsBeforeReseed['testimonials'], Testimonial::count());
        $this->assertSame($countsBeforeReseed['transactions'], Transaction::count());
    }
}
