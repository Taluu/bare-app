<?php

namespace Bare\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Swarrot\Consumer;
use Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor;

/**
 * RabbitMQ Consumer
 *
 * Consumer for rabbitmq messages. Dispatch it to the right command.
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class ConsumerCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this->setName('wisembly:amqp:consume')
             ->setDescription('Consume all messages launched through an AMQP gate');

        $this->addArgument('gate', InputArgument::REQUIRED, 'AMQP Gate to use');
        $this->addOption('poll-interval', null, InputOption::VALUE_REQUIRED, 'poll interval, in micro-seconds', 50000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $gate = $input->getArgument('gate');
        $gate = $container->get('amqp.gates')->get($gate);

        $kernel = $container->get('kernel');
        $logger = $container->get('logger');
        $broker = $container->get('amqp.broker');

        $provider = $broker->getProvider($gate);
        $producer = $broker->getProducer($gate);

        // Wrap processor in an Swarrot ExceptionCatcherProcessor to avoid breaking processor if an error occurs
        $processor = null;
        //$consumer  = new Consumer($provider, new ExceptionCatcherProcessor($processor, $logger));
        //$consumer->consume(['poll_interval' => $input->getOption('poll-interval')]);

        throw new \BadMethodCallException('No processors implemented... aborting');
    }
}

