<?php

namespace MtoOrderPicking\Console;

use MtoOrderPicking\OrderExport\OrderConverter;
use MtoOrderPicking\OrderExport\OrderExporter;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: "mto:order:export")]
class OrderExportCommand extends Command
{

    public function __construct(
        protected MessageBusInterface $messageBus,
        protected EntityRepository $orderRepository,
        protected OrderExporter $orderExporter,
        protected OrderConverter $orderConverter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('orderNumber', InputArgument::REQUIRED);
        $this->addOption('dump', InputArgument::OPTIONAL);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orderNumber = $input->getArgument('orderNumber');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderNumber', $orderNumber));
        $criteria->addAssociations(
            ['orderCustomer', 'billingAddress', 'billingAddress.country']
        );
        $criteria->addAssociations([
            'deliveries', 'deliveries.shippingOrderAddress', 'deliveries.shippingOrderAddress.country'
        ]);
        $criteria->addAssociations(['lineItems', 'lineItems.product']);

        $context = Context::createDefaultContext();
        $orderEntity = $this->orderRepository->search($criteria, $context)->first();

        if (! $orderEntity instanceof OrderEntity) {
            $output->writeln(sprintf('Order with %s number not found', $orderNumber));

            return self::FAILURE;
        }

        if ($input->getOption('dump')) {
            $convertedOrder = $this->orderConverter->convert($orderEntity);

            $output->write($convertedOrder);

            return self::SUCCESS;
        }

        $this->orderExporter->export($orderEntity->getId(), $context);

        return self::SUCCESS;
    }

}