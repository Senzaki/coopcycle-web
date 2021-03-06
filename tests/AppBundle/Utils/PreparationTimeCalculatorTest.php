<?php

namespace Tests\AppBundle\Utils;

use AppBundle\Entity\Restaurant;
use AppBundle\Sylius\Order\OrderInterface;
use AppBundle\Utils\PreparationTimeCalculator;
use PHPUnit\Framework\TestCase;

class PreparationTimeCalculatorTest extends TestCase
{
    private $config;

    public function setUp()
    {
        $this->config = [
            'restaurant.state == "rush" and order.itemsTotal < 2000'        => '20 minutes',
            'restaurant.state == "rush" and order.itemsTotal in 2000..5000' => '30 minutes',
            'restaurant.state == "rush" and order.itemsTotal > 5000'        => '45 minutes',
            'order.itemsTotal <= 2000'                                      => '10 minutes',
            'order.itemsTotal in 2000..5000'                                => '15 minutes',
            'order.itemsTotal > 5000'                                       => '30 minutes',
        ];
    }

    private function createOrder($total, $state = 'normal')
    {
        $restaurant = new Restaurant();
        $restaurant->setState($state);

        $order = $this->prophesize(OrderInterface::class);
        $order
            ->getRestaurant()
            ->willReturn($restaurant);
        $order
            ->getItemsTotal()
            ->willReturn($total);

        return $order->reveal();
    }

    public function calculateProvider()
    {
        return [
            // state = normal
            [
                $this->createOrder(1500),
                '10 minutes',
            ],
            [
                $this->createOrder(3000),
                '15 minutes',
            ],
            [
                $this->createOrder(6000),
                '30 minutes',
            ],
            // state = rush
            [
                $this->createOrder(1500, 'rush'),
                '20 minutes',
            ],
            [
                $this->createOrder(3000, 'rush'),
                '30 minutes',
            ],
            [
                $this->createOrder(6000, 'rush'),
                '45 minutes',
            ],
        ];
    }

    /**
     * @dataProvider calculateProvider
     */
    public function testCalculate(
        OrderInterface $order,
        $expectedPrepatationTime)
    {
        $this->calculator = new PreparationTimeCalculator($this->config);
        $this->assertEquals($expectedPrepatationTime, $this->calculator->calculate($order));
    }
}
