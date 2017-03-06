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

use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

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
            ->addOption('region', 'r', InputOption::VALUE_REQUIRED, 'Region')
            ->addOption('prefix', 'p', InputOption::VALUE_REQUIRED, 'Prefix Key');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bucket = $input->getArgument('bucket');
        $source = $input->getArgument('source');
        $region = $input->getOption('region');
        $prefix = $input->getOption('prefix');

        $s3 = (new S3Config($region))->client();
        $key = $prefix . basename($source);

        // Multipart Uploader
        $uploader = new MultipartUploader($s3, $source, [
            'bucket' => $bucket,
            'key'    => $key,
        ]);

        $output->writeln("Start uploading...");

        // Recover from error
        // We try to recover until specific times
        $maxRecoveries = 10;
        $numRecovery = 0;
        do {
            try {
                $result = $uploader->upload();
                $output->writeln($result['ObjectURL']);
            } catch (MultipartUploadException $e) {
                $numRecovery++;
                $output->writeln($e->getMessage());
                $output->writeln("Trying to recover upload... ({$numRecovery})");
                $uploader = new MultipartUploader($s3, $source, [
                    'state' => $e->getState(),
                ]);
            }
        } while (!isset($result) && $numRecovery < $maxRecoveries);

        if (empty($result))
            $output->writeln("Upload failed!");

        $output->writeln("Upload success.");

        /*if (empty($expire)) {
            $output->writeln("No expiration set.");
            return;
        }
        
        $output->writeln("Setting expiration to {$expire} days...");

        // Once it is uploaded we set the expiration days
        try {
            $result = $s3->putBucketLifecycleConfiguration([
                'Bucket' => $bucket,
                'LifecycleConfiguration' => [
                    'Rules' => [
                        [
                            'Expiration' => [
                                'Days' => $expire,
                            ],
                            'Prefix' => $key,
                            'Status' => 'Enabled',
                        ]
                    ]
                ]
            ]);
            $output->writeln("Setting expiration done.");
        } catch (S3Exception $e) {
            $output->writeln($e->getMessage());
        }*/
    }
}
