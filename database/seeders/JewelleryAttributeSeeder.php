<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class JewelleryAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Metal Type',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['Gold', 'Silver', 'Platinum', 'Rose Gold', 'White Gold', 'Copper', 'Brass', 'Stainless Steel'],
            ],
            [
                'name' => 'Metal Purity',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['24K', '22K', '18K', '14K', '10K', '950 Platinum', '925 Sterling Silver'],
            ],
            [
                'name' => 'Metal Color',
                'type' => 'color',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => [
                    ['value' => 'Yellow Gold', 'color_code' => '#FFD700'],
                    ['value' => 'White Gold', 'color_code' => '#E8E8E8'],
                    ['value' => 'Rose Gold', 'color_code' => '#B76E79'],
                    ['value' => 'Silver', 'color_code' => '#C0C0C0'],
                    ['value' => 'Platinum', 'color_code' => '#E5E4E2'],
                    ['value' => 'Two-Tone', 'color_code' => '#DAA520'],
                ],
            ],
            [
                'name' => 'Stone Type',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['Diamond', 'Ruby', 'Emerald', 'Sapphire', 'Pearl', 'Amethyst', 'Topaz', 'Garnet', 'Opal', 'Tanzanite', 'Turquoise', 'No Stone'],
            ],
            [
                'name' => 'Diamond Clarity',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2', 'I1', 'I2'],
            ],
            [
                'name' => 'Diamond Cut',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['Excellent', 'Very Good', 'Good', 'Fair'],
            ],
            [
                'name' => 'Diamond Color',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'],
            ],
            [
                'name' => 'Stone Shape',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['Round', 'Princess', 'Oval', 'Cushion', 'Emerald Cut', 'Pear', 'Marquise', 'Heart', 'Radiant', 'Asscher'],
            ],
            [
                'name' => 'Ring Size',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['Adjustable', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24'],
            ],
            [
                'name' => 'Bangle Size',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['2.2', '2.4', '2.6', '2.8', '2.10', '2.12'],
            ],
            [
                'name' => 'Necklace Length',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['14 inch', '16 inch', '18 inch', '20 inch', '22 inch', '24 inch', '30 inch'],
            ],
            [
                'name' => 'Occasion',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['Wedding', 'Engagement', 'Daily Wear', 'Party', 'Festive', 'Office Wear', 'Anniversary', 'Gift'],
            ],
            [
                'name' => 'Certification',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['BIS Hallmark', 'IGI Certified', 'GIA Certified', 'SGL Certified', 'HRD Certified'],
            ],
            [
                'name' => 'Gender',
                'type' => 'select',
                'is_filterable' => true,
                'is_visible' => true,
                'values' => ['Women', 'Men', 'Unisex', 'Kids'],
            ],
        ];

        foreach ($attributes as $attrData) {
            $values = $attrData['values'];
            unset($attrData['values']);

            $attribute = Attribute::firstOrCreate(
                ['name' => $attrData['name']],
                $attrData
            );

            foreach ($values as $position => $value) {
                if (is_array($value)) {
                    AttributeValue::firstOrCreate(
                        ['attribute_id' => $attribute->id, 'value' => $value['value']],
                        ['color_code' => $value['color_code'], 'position' => $position]
                    );
                } else {
                    AttributeValue::firstOrCreate(
                        ['attribute_id' => $attribute->id, 'value' => $value],
                        ['position' => $position]
                    );
                }
            }
        }
    }
}
