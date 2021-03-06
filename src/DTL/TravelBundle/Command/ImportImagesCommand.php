<?php

namespace DTL\TravelBundle\Command;

use Sonata\MediaBundle\Command\AddMassMediaCommand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PHPCR\Util\NodeHelper;
use Symfony\Component\Console\Input\InputOption;

class ImportImagesCommand extends AddMassMediaCommand
{
    public function configure()
    {
        $this->setName('travel:image-import')
            ->setDescription('Add images and their metadatas to db')
            ->setDefinition(array(
                new InputArgument('dir', InputArgument::REQUIRED, 'Directory with medias'),
            ));

        $this->addOption('force-date', NULL, InputOption::VALUE_OPTIONAL, 'Force date on imported medias');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        $finder = Finder::create();
        $finder->in(array($dir));
        $finder->name('*.jpg');
        $dm = $this->getContainer()->get('doctrine_phpcr.odm.default_document_manager');

        NodeHelper::createPath($dm->getPhpcrSession(), '/cms/media');

        foreach ($finder as $file) {
            $output->writeln('<info>Importing: '.$file.'</info>');
            $exif = exif_read_data($file->getRealPath(),'IFD0',true);
            $metadata = array();

            foreach ($exif as $section => $data) {
                foreach ($data as $key => $value) {
                    if (!is_array($value)) {
                        $value = preg_replace('/[^(\x20-\x7F)]*/','', $value);
                        $metadata[$section.'-'.$key] = $value;
                    }
                }
            }

            $media = $this->getMediaManager()->create();
            $media->setName($file->getFileName());
            $media->setProviderName('sonata.media.provider.image');
            $media->setBinaryContent($file->getRealPath());

            if (isset($exif['GPS'])) {
                $media->setLatitude($this->gpsDataToDecimal(
                    $exif['GPS']['GPSLatitude']
                ));
                $media->setLongitude($this->gpsDataToDecimal(
                    $exif['GPS']['GPSLongitude']
                ));
                $media->setAltitude($this->gpsFractionToDecimal(
                    $exif['GPS']['GPSAltitude']
                ));
            }

            if (isset($exif['FILE'])) {
                if ($forceDate = $input->getOption('force-date')) {
                    $timestamp = new \DateTime($forceDate);
                } else {
                    $timestamp = new \DateTime($exif['EXIF']['DateTimeOriginal']);
                }

                $media->setTimestamp($timestamp);
            }

            $media->setContext('default');

            try {
                $this->getMediaManager()->save($media);
            } catch (\ModelManagerException $e) {
                $output->writeln('<error>Error: [MM]'.$e->getPrevious()->getMessage().'</error>');
            } catch (\Exception $e) {
                $output->writeln('<error>Error: '.$e->getPrevious()->getMessage().'</error>');
            }

            $output->writeln(sprintf(' > %s - %s', $media->getId(), $media->getName()));
        }
        $output->writeln("Done!");
        return;
    }

    protected function gpsDataToDecimal($gpsData)
    {
        list($wholeDegrees, $minutes, $seconds) = $gpsData;
        $wholeDegrees = $this->gpsFractionToDecimal($wholeDegrees);
        $minutes = $this->gpsFractionToDecimal($minutes);
        $seconds = $this->gpsFractionToDecimal($seconds);

        return $wholeDegrees + ($minutes / 60) + ($seconds / 3600);
    }

    protected function gpsFractionToDecimal($fractionString)
    {
        $parts = explode('/', $fractionString);
        $decimal = (integer) $parts[0] / (integer) $parts[1];
        return $decimal;
    }
}
