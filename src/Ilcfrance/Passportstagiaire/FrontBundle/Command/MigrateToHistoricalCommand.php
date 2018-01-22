<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Trainee;

/**
 *
 * @author sasedev
 */
class MigrateToHistoricalCommand extends Command
{

    /**
     *
     * @var ManagerRegistry
     */
    private $registry;

    /**
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('ilc:migrate')->setDescription('Migrate Work Records under new historicals.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', '0');

        $em = $this->registry->getManager('mig');
        $cache = $em->getCache();
        $trainees = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->findAll();
        $output->writeln('Trainees found :' . \count($trainees));
        foreach ($trainees as $trainee) {
            if (\count($trainee->getHistoricals()) == 0) {
                $hist = new TraineeHistorical();
                $hist->setTrainee($trainee);
                $hist->setYear("2016");
                $hist->setOrigine($trainee->getOrigine());
                $hist->setCourses($trainee->getCourses());
                $hist->setNeeds($trainee->getNeeds());
                $hist->setInitLevel($trainee->getInitLevel());
                $hist->setLevel($trainee->getLevel());
                $em->persist($hist);
                $output->writeln('New TraineeHistorical :' . $hist->getFullName());
            }
        }
        $em->flush();
        $cntRecords = 0;
        foreach ($trainees as $trainee) {
            if ($cache) {
                $cache->evictCollection(Trainee::class, 'historicals', $trainee->getId());
            }
            foreach ($trainee->getHistoricals() as $hist) {
                foreach ($trainee->getRecords() as $record) {
                    if (null == $record->getHistorical()) {
                        $record->setHistorical($hist);
                        $em->persist($record);
                        $output->writeln('Update TraineeRecord :' . $record->getFullName());
                        $cntRecords++;
                    }
                }
            }
        }
        $output->writeln('Flushing TraineeRecords :' . $cntRecords);
        $em->flush();
    }
}

