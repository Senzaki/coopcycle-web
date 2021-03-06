<?php

namespace AppBundle\Twig;

use AppBundle\Sylius\Order\OrderInterface;
use AppBundle\Sylius\Order\OrderTransitions;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;

class OrderStateResolver
{
    private $stateMachineFactory;

    public function __construct(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function orderCanTransitionFilter(OrderInterface $order, $transition)
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);
        return $stateMachine->can($transition);
    }
}
