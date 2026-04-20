<?php

namespace Database\Seeders;

use App\Models\BoardingHouse;
use App\Models\Bonus;
use App\Models\Category;
use App\Models\City;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Testimonial;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class NgekosSeeder extends Seeder
{
    private const IMAGE_ROOT = 'seeders/ngekos';

    public function run(): void
    {
        $cities = $this->seedCities();
        $categories = $this->seedCategories();

        foreach ($this->boardingHouseSeeds() as $boardingHouseSeed) {
            $boardingHouse = BoardingHouse::withTrashed()->updateOrCreate(
                ['slug' => $boardingHouseSeed['slug']],
                [
                    'name' => $boardingHouseSeed['name'],
                    'slug' => $boardingHouseSeed['slug'],
                    'thumbnail' => $this->copyImage(
                        $boardingHouseSeed['thumbnail_source'],
                        "boarding-houses/{$boardingHouseSeed['slug']}.png",
                    ),
                    'city_id' => $cities[$boardingHouseSeed['city_slug']]->id,
                    'category_id' => $categories[$boardingHouseSeed['category_slug']]->id,
                    'description' => $boardingHouseSeed['description'],
                    'price' => $boardingHouseSeed['price'],
                    'address' => $boardingHouseSeed['address'],
                    'deleted_at' => null,
                ],
            );

            foreach ($boardingHouseSeed['rooms'] as $roomSeed) {
                $room = Room::withTrashed()->updateOrCreate(
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'name' => $roomSeed['name'],
                    ],
                    [
                        'room_type' => $roomSeed['room_type'],
                        'square_feet' => $roomSeed['square_feet'],
                        'capacity' => $roomSeed['capacity'],
                        'price_per_month' => $roomSeed['price_per_month'],
                        'is_avaible' => $roomSeed['is_avaible'],
                        'deleted_at' => null,
                    ],
                );

                foreach ($roomSeed['images'] as $index => $imageSource) {
                    $imagePath = $this->copyImage(
                        $imageSource,
                        "rooms/{$boardingHouseSeed['slug']}-room-".($index + 1).'.png',
                    );

                    RoomImage::withTrashed()->updateOrCreate(
                        [
                            'room_id' => $room->id,
                            'image' => $imagePath,
                        ],
                        [
                            'image' => $imagePath,
                            'deleted_at' => null,
                        ],
                    );
                }
            }

            foreach ($boardingHouseSeed['bonuses'] as $bonusSeed) {
                Bonus::withTrashed()->updateOrCreate(
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'name' => $bonusSeed['name'],
                    ],
                    [
                        'image' => $this->copyImage(
                            $bonusSeed['image_source'],
                            "bonuses/{$boardingHouseSeed['slug']}-{$bonusSeed['slug']}.png",
                        ),
                        'description' => $bonusSeed['description'],
                        'deleted_at' => null,
                    ],
                );
            }

            foreach ($boardingHouseSeed['testimonials'] as $testimonialSeed) {
                Testimonial::withTrashed()->updateOrCreate(
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'name' => $testimonialSeed['name'],
                    ],
                    [
                        'photo' => $this->copyImage(
                            $testimonialSeed['photo_source'],
                            "testimonials/{$boardingHouseSeed['slug']}-{$testimonialSeed['slug']}.png",
                        ),
                        'content' => $testimonialSeed['content'],
                        'rating' => $testimonialSeed['rating'],
                        'deleted_at' => null,
                    ],
                );
            }

            foreach ($boardingHouseSeed['transactions'] as $transactionSeed) {
                $room = Room::query()
                    ->where('boarding_house_id', $boardingHouse->id)
                    ->where('name', $transactionSeed['room_name'])
                    ->firstOrFail();

                Transaction::withTrashed()->updateOrCreate(
                    ['code' => $transactionSeed['code']],
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'room_id' => $room->id,
                        'name' => $transactionSeed['name'],
                        'email' => $transactionSeed['email'],
                        'phone_number' => $transactionSeed['phone_number'],
                        'payment_method' => $transactionSeed['payment_method'],
                        'payment_status' => $transactionSeed['payment_status'],
                        'start_date' => $transactionSeed['start_date'],
                        'duration' => $transactionSeed['duration'],
                        'total_amount' => $transactionSeed['total_amount'],
                        'transaction_date' => $transactionSeed['transaction_date'],
                        'deleted_at' => null,
                    ],
                );
            }
        }
    }

    /**
     * @return array<string, City>
     */
    private function seedCities(): array
    {
        $cities = [];

        foreach ($this->citySeeds() as $citySeed) {
            $cities[$citySeed['slug']] = City::withTrashed()->updateOrCreate(
                ['slug' => $citySeed['slug']],
                [
                    'name' => $citySeed['name'],
                    'image' => $this->copyImage(
                        $citySeed['image_source'],
                        "cities/{$citySeed['slug']}.png",
                    ),
                    'deleted_at' => null,
                ],
            );
        }

        return $cities;
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $categories = [];

        foreach ($this->categorySeeds() as $categorySeed) {
            $categories[$categorySeed['slug']] = Category::withTrashed()->updateOrCreate(
                ['slug' => $categorySeed['slug']],
                [
                    'name' => $categorySeed['name'],
                    'image' => $this->copyImage(
                        $categorySeed['image_source'],
                        "categories/{$categorySeed['slug']}.png",
                    ),
                    'deleted_at' => null,
                ],
            );
        }

        return $categories;
    }

    private function copyImage(string $sourceRelativePath, string $destinationRelativePath): string
    {
        $sourcePath = base_path("design/src/assets/images/{$sourceRelativePath}");
        $destinationPath = self::IMAGE_ROOT."/{$destinationRelativePath}";

        if (! File::exists($sourcePath)) {
            throw new RuntimeException("Seeder image source not found: {$sourcePath}");
        }

        if (! Storage::disk('public')->exists($destinationPath)) {
            Storage::disk('public')->put($destinationPath, File::get($sourcePath));
        }

        return $destinationPath;
    }

    private function citySeeds(): array
    {
        return [
            [
                'name' => 'Bogor',
                'slug' => 'bogor',
                'image_source' => 'thumbnails/city-1.png',
            ],
            [
                'name' => 'California',
                'slug' => 'california',
                'image_source' => 'thumbnails/city-2.png',
            ],
            [
                'name' => 'Jakarta',
                'slug' => 'jakarta',
                'image_source' => 'thumbnails/city-1.png',
            ],
            [
                'name' => 'Bandung',
                'slug' => 'bandung',
                'image_source' => 'thumbnails/city-3.png',
            ],
            [
                'name' => 'Paris',
                'slug' => 'paris',
                'image_source' => 'thumbnails/city-4.png',
            ],
            [
                'name' => 'Singapore City',
                'slug' => 'singapore-city',
                'image_source' => 'thumbnails/city-2.png',
            ],
        ];
    }

    private function categorySeeds(): array
    {
        return [
            [
                'name' => 'Flats',
                'slug' => 'flats',
                'image_source' => 'thumbnails/flats.png',
            ],
            [
                'name' => 'Villas',
                'slug' => 'villas',
                'image_source' => 'thumbnails/villas.png',
            ],
            [
                'name' => 'Hotel',
                'slug' => 'hotel',
                'image_source' => 'thumbnails/hotel.png',
            ],
            [
                'name' => 'Apartments',
                'slug' => 'apartments',
                'image_source' => 'thumbnails/apartments.png',
            ],
            [
                'name' => 'Buildings',
                'slug' => 'buildings',
                'image_source' => 'thumbnails/buildings.png',
            ],
        ];
    }

    private function boardingHouseSeeds(): array
    {
        $sharedBonuses = [
            [
                'slug' => 'clean-laundry',
                'name' => 'Clean Laundry',
                'image_source' => 'thumbnails/bonus-1.png',
                'description' => 'Super Fast • 4 People',
            ],
            [
                'slug' => 'healthy-catering',
                'name' => 'Healthy Catering',
                'image_source' => 'thumbnails/bonus-2.png',
                'description' => 'Animal Base • 4 People',
            ],
            [
                'slug' => 'coworking-space',
                'name' => 'Coworking Space',
                'image_source' => 'thumbnails/bonus-3.png',
                'description' => 'Comfortable • 4 People',
            ],
        ];

        return [
            [
                'name' => 'Tumbuh Tentram Berada Rumah Nenek',
                'slug' => 'tumbuh-tentram-berada-rumah-nenek',
                'thumbnail_source' => 'thumbnails/kos-1.png',
                'city_slug' => 'singapore-city',
                'category_slug' => 'hotel',
                'description' => 'With fast WiFi and comfortable kitchen, this apartment is ready to support focused work and restful living.',
                'price' => 1493593,
                'address' => '22 Orchard Road, Singapore City',
                'rooms' => [
                    [
                        'name' => 'Deluxe Room',
                        'room_type' => 'flat',
                        'square_feet' => 184,
                        'capacity' => 1,
                        'price_per_month' => 793444,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-1.png',
                        ],
                    ],
                    [
                        'name' => 'Executive Room',
                        'room_type' => 'flat',
                        'square_feet' => 184,
                        'capacity' => 2,
                        'price_per_month' => 793444,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-2.png',
                        ],
                    ],
                    [
                        'name' => 'President Estate',
                        'room_type' => 'flat',
                        'square_feet' => 184,
                        'capacity' => 4,
                        'price_per_month' => 793444,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-3.png',
                        ],
                    ],
                ],
                'bonuses' => $sharedBonuses,
                'testimonials' => [
                    [
                        'slug' => 'samina-ryin',
                        'name' => 'Samina Ryin',
                        'photo_source' => 'photos/sami.png',
                        'content' => 'Enak banget ngekos di sini sampe lupa rumah emak saking nyamannya lol...',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => 'NGKBWA1996',
                        'room_name' => 'Executive Room',
                        'name' => 'Samina Ryin',
                        'email' => 'samina@example.com',
                        'phone_number' => '081234567890',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2024-09-10',
                        'duration' => 6,
                        'total_amount' => 4760664,
                        'transaction_date' => '2024-09-09',
                    ],
                ],
            ],
            [
                'name' => 'Rumah Senja Harmoni',
                'slug' => 'rumah-senja-harmoni',
                'thumbnail_source' => 'thumbnails/kos-2.png',
                'city_slug' => 'bogor',
                'category_slug' => 'flats',
                'description' => 'A calm boarding house with warm interiors, airy windows, and easy access to the city center.',
                'price' => 4593444,
                'address' => '10 Jalan Pajajaran, Bogor',
                'rooms' => [
                    [
                        'name' => 'Starter Room',
                        'room_type' => 'flat',
                        'square_feet' => 160,
                        'capacity' => 2,
                        'price_per_month' => 725000,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-2.png',
                        ],
                    ],
                ],
                'bonuses' => $sharedBonuses,
                'testimonials' => [
                    [
                        'slug' => 'rahma-putri',
                        'name' => 'Rahma Putri',
                        'photo_source' => 'photos/sami.png',
                        'content' => 'Lokasinya strategis dan kamar selalu bersih setiap minggu.',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => 'NGKBGR2001',
                        'room_name' => 'Starter Room',
                        'name' => 'Rahma Putri',
                        'email' => 'rahma@example.com',
                        'phone_number' => '081111111111',
                        'payment_method' => 'down_payment',
                        'payment_status' => 'pending',
                        'start_date' => '2024-10-01',
                        'duration' => 3,
                        'total_amount' => 2175000,
                        'transaction_date' => '2024-09-15',
                    ],
                ],
            ],
            [
                'name' => 'Kos Melati Residence',
                'slug' => 'kos-melati-residence',
                'thumbnail_source' => 'thumbnails/kos-3.png',
                'city_slug' => 'jakarta',
                'category_slug' => 'apartments',
                'description' => 'Modern co-living for urban professionals with a practical layout and a quiet atmosphere.',
                'price' => 3299000,
                'address' => '88 Jalan Sudirman, Jakarta',
                'rooms' => [
                    [
                        'name' => 'Urban Room',
                        'room_type' => 'apartment',
                        'square_feet' => 172,
                        'capacity' => 2,
                        'price_per_month' => 950000,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-1.png',
                        ],
                    ],
                ],
                'bonuses' => $sharedBonuses,
                'testimonials' => [
                    [
                        'slug' => 'dian-lestari',
                        'name' => 'Dian Lestari',
                        'photo_source' => 'photos/sami.png',
                        'content' => 'Internetnya stabil buat kerja remote dan fasilitas dapurnya lengkap.',
                        'rating' => 4,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => 'NGKJKT3002',
                        'room_name' => 'Urban Room',
                        'name' => 'Dian Lestari',
                        'email' => 'dian@example.com',
                        'phone_number' => '082222222222',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2024-11-05',
                        'duration' => 4,
                        'total_amount' => 3800000,
                        'transaction_date' => '2024-10-30',
                    ],
                ],
            ],
            [
                'name' => 'Puri Nyaman Sejahtera',
                'slug' => 'puri-nyaman-sejahtera',
                'thumbnail_source' => 'thumbnails/kos-4.png',
                'city_slug' => 'bandung',
                'category_slug' => 'villas',
                'description' => 'Spacious rooms and green surroundings for residents who want a relaxed stay in Bandung.',
                'price' => 3895000,
                'address' => '15 Jalan Dago, Bandung',
                'rooms' => [
                    [
                        'name' => 'Garden Suite',
                        'room_type' => 'villa',
                        'square_feet' => 210,
                        'capacity' => 3,
                        'price_per_month' => 1100000,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-3.png',
                        ],
                    ],
                ],
                'bonuses' => $sharedBonuses,
                'testimonials' => [
                    [
                        'slug' => 'eka-safitri',
                        'name' => 'Eka Safitri',
                        'photo_source' => 'photos/sami.png',
                        'content' => 'Udara sejuk dan area coworking-nya bikin betah kerja setiap hari.',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => 'NGKBDG4003',
                        'room_name' => 'Garden Suite',
                        'name' => 'Eka Safitri',
                        'email' => 'eka@example.com',
                        'phone_number' => '083333333333',
                        'payment_method' => 'down_payment',
                        'payment_status' => 'pending',
                        'start_date' => '2024-12-01',
                        'duration' => 2,
                        'total_amount' => 2200000,
                        'transaction_date' => '2024-11-20',
                    ],
                ],
            ],
            [
                'name' => 'Griya Cemara Asri',
                'slug' => 'griya-cemara-asri',
                'thumbnail_source' => 'thumbnails/kos-5.png',
                'city_slug' => 'paris',
                'category_slug' => 'buildings',
                'description' => 'A bright and compact living space with an elegant front desk and a secure environment.',
                'price' => 4120000,
                'address' => '31 Rue du Centre, Paris',
                'rooms' => [
                    [
                        'name' => 'Sky Room',
                        'room_type' => 'building',
                        'square_feet' => 190,
                        'capacity' => 2,
                        'price_per_month' => 1200000,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-2.png',
                        ],
                    ],
                ],
                'bonuses' => $sharedBonuses,
                'testimonials' => [
                    [
                        'slug' => 'lina-maharani',
                        'name' => 'Lina Maharani',
                        'photo_source' => 'photos/sami.png',
                        'content' => 'Tempatnya premium tapi tetap nyaman untuk tinggal jangka panjang.',
                        'rating' => 4,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => 'NGKPRS5004',
                        'room_name' => 'Sky Room',
                        'name' => 'Lina Maharani',
                        'email' => 'lina@example.com',
                        'phone_number' => '084444444444',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2025-01-10',
                        'duration' => 5,
                        'total_amount' => 6000000,
                        'transaction_date' => '2025-01-01',
                    ],
                ],
            ],
            [
                'name' => 'Villa Bintang Timur',
                'slug' => 'villa-bintang-timur',
                'thumbnail_source' => 'thumbnails/kos-6.png',
                'city_slug' => 'california',
                'category_slug' => 'villas',
                'description' => 'A private and airy home base with a wide room layout and strong natural light.',
                'price' => 4350000,
                'address' => '7 Sunset Avenue, California',
                'rooms' => [
                    [
                        'name' => 'Sunrise Room',
                        'room_type' => 'villa',
                        'square_feet' => 205,
                        'capacity' => 4,
                        'price_per_month' => 1300000,
                        'is_avaible' => true,
                        'images' => [
                            'thumbnails/room-1.png',
                        ],
                    ],
                ],
                'bonuses' => $sharedBonuses,
                'testimonials' => [
                    [
                        'slug' => 'raka-pratama',
                        'name' => 'Raka Pratama',
                        'photo_source' => 'photos/sami.png',
                        'content' => 'Cocok untuk keluarga kecil dan akses lingkungannya juga aman.',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => 'NGKCAL6005',
                        'room_name' => 'Sunrise Room',
                        'name' => 'Raka Pratama',
                        'email' => 'raka@example.com',
                        'phone_number' => '085555555555',
                        'payment_method' => 'down_payment',
                        'payment_status' => 'pending',
                        'start_date' => '2025-02-01',
                        'duration' => 6,
                        'total_amount' => 7800000,
                        'transaction_date' => '2025-01-25',
                    ],
                ],
            ],
        ];
    }
}
