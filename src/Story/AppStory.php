<?php

namespace App\Story;

use App\Factory\ProductFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        // Whiskey products
        ProductFactory::createOne([
            'imagePath' => 'https://images-na.ssl-images-amazon.com/images/I/419Jmp5klRL.jpg',
            'category' => 'Viskis',
            'title' => 'Jack Daniels',
            'description' => 'Skanus Viskis',
            'price' => 5.5
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://thinkliquor.com/conor-mcgreggor-proper-twelve-irish-whiskey',
            'category' => 'Viskis',
            'title' => 'Proper 12 No.',
            'description' => 'Skanus Airiškas Viskis',
            'price' => 9
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://img.thewhiskyexchange.com/900/liq_jag1.jpg',
            'category' => 'Viskis',
            'title' => 'Jagermeister',
            'description' => 'Skanus kaip Pertusinas',
            'price' => 8
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://d256619kyxncpv.cloudfront.net/gui/img/2018/03/07/11/2018030711_johnnie_walker_jane_walker_edition__original.png',
            'category' => 'Viskis',
            'title' => 'Johnny Walker: Black label',
            'description' => 'Skanusiausias bei geriausias',
            'price' => 14
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://img.thewhiskyexchange.com/900/irish_jam1.jpg',
            'category' => 'Viskis',
            'title' => 'Jameson',
            'description' => 'Skanus airių viskis bei tinka kola',
            'price' => 15.72
        ]);

        // Vodka products
        ProductFactory::createOne([
            'imagePath' => 'https://www.vynoguru.lt/media/catalog/product/cache/2/image/800x600/9df78eab33525d08d6e5fb8d27136e95/l/i/lithuanian_vodka_originali_1_l.jpg',
            'category' => 'Degtinė',
            'title' => 'Lietuviška degtinė',
            'description' => 'Skani lietuviška degtinė',
            'price' => 10.50
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://www.cellarbration.com.sg/content/images/thumbs/0001467_grey-goose-original-700ml_540.jpeg',
            'category' => 'Degtinė',
            'title' => 'Grey Goose Original',
            'description' => 'Tikra, orginali prancūziška degtinė',
            'price' => 7.8
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://img.thewhiskyexchange.com/900/vodka_abs66.jpg',
            'category' => 'Degtinė',
            'title' => 'Absolut Vodka',
            'description' => 'Skani bei kieta degtinė',
            'price' => 16
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://img.thewhiskyexchange.com/900/vodka_wyb1.jpg',
            'category' => 'Degtinė',
            'title' => 'Wybrodawa vodka',
            'description' => 'Lenkiška degtinė',
            'price' => 13.40
        ]);

        ProductFactory::createOne([
            'imagePath' => 'https://www.berevita.com/pub/media/catalog/product/cache/image/1000x1320/e9c3970ab036de70892d86c6d221abfe/3/2/32027.jpg',
            'category' => 'Degtinė',
            'title' => 'Finlandia',
            'description' => 'Tikra suomių degtinė',
            'price' => 12.57
        ]);
    }
}
