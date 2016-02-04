<?php
/*
 * This file is part of the Symfony package.
 * It's been extracted from the Framework Bundle
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bare;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Application.
 * Copied and modified from Symfony's Framework bundle
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Application extends BaseApplication
{
    private $kernel;
    private $commandsRegistered = false;

    /**
     * Constructor.
     */
    public function __construct(AppKernel $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct('Bare App', sprintf('%s - %s/%s%s', AppKernel::VERSION, $kernel->getName(), $kernel->getEnvironment(), ($kernel->isDebug() ? '/debug' : '')));

        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $kernel->getEnvironment()));
        $this->getDefinition()->addOption(new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'));
    }

    /**
     * Gets the Kernel associated with this Console.
     *
     * @return KernelInterface A KernelInterface instance
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /** {@inheritDoc} */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->kernel->boot();

        if (!$this->commandsRegistered) {
            $this->registerCommands();

            $this->commandsRegistered = true;
        }

        $container = $this->kernel->getContainer();

        foreach ($this->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }
        }

        $this->setDispatcher($container->get('event_dispatcher'));

        return parent::doRun($input, $output);
    }

    protected function registerCommands()
    {
        $container = $this->kernel->getContainer();

        foreach ($this->kernel->getBundles() as $bundle) {
            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($this);
            }
        }

        if ($container->hasParameter('console.command.ids')) {
            foreach ($container->getParameter('console.command.ids') as $id) {
                $this->add($container->get($id));
            }
        }
    }
}
