<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('hydra:twitter:sync')
            ->setDescription('Sync tweets with your database')
            ->addArgument(
                'requestId',
                InputArgument::OPTIONAL,
                'Which request should be executed (comma-separated, refreshtime ignored)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('virtual_identity_twitter');

        $output->write('Syncing... ');

        $requestId = $input->getArgument('requestId');
        if ($requestId) {
            $output->write('only api request with id '.$requestId.'... ');
            $service->syncDatabase(explode(',', $requestId));
        } else {
            $service->syncDatabase();
        }

        $output->writeln('Done.');
    }
}