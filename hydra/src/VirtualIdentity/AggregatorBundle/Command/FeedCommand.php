<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FeedCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('hydra:feed')
            ->setDescription('Reads the current feed from the database and prints it.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aggregator = $this->getContainer()->get('virtual_identity_aggregator');

        $output->write('Fetching feed... ');
        $feed = $aggregator->getFeed();
        $output->writeln('Done.');

        foreach ($feed as $entry) {
            // $entry is a UnifiedSocialEntityInterface
            $output->write($entry->getCreated()->format('Y-m-d H:i:s').'  ');
            $output->writeln($entry->getText());
        }
    }
}