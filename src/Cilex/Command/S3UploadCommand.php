<?php

/*
 * This file is part of the Cilex framework.
 *
 * (c) Mike van Riel <mike.vanriel@naenius.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cilex\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Cilex\Provider\Console\Command;
use Aws\S3\S3Client;

/**
 * Example command for testing purposes.
 */
class S3UploadCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('s3:upload')
            ->setDescription('Upload file to S3')
            ->addArgument('bucket', InputArgument::REQUIRED, 'Which bucket do you want to upload into?')
            ->addArgument('source', InputArgument::REQUIRED, 'Absolute path of the file on the disk')
            ->addArgument('region', InputArgument::OPTIONAL, 'Region');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $s3 = (new S3Config())->client();

        $bucket = $input->getArgument('bucket');
        $source = $input->getArgument('source');

        try {
            $command = $s3->getCommand('PutObject', [
                'Bucket' => $bucket,
                'Key'    => basename($source),
                'Body'   => fopen($source, 'r')
            ]);

            $result = $s3->execute($command);
            $output->writeln('SUCCESS');
        } catch (\Aws\S3\Exception\S3Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
