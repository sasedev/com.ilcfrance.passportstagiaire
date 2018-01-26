<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\Trainee;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecord;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\AddTForm as TraineeAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\ImportTForm as TraineeImportTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateAddressTForm as TraineeUpdateAddressTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateCoursesTForm as TraineeUpdateCoursesTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateEmailTForm as TraineeUpdateEmailTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateFirstNameTForm as TraineeUpdateFirstNameTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateInitLevelTForm as TraineeUpdateInitLevelTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateJobTForm as TraineeUpdateJobTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateLastNameTForm as TraineeUpdateLastNameTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateLevelTForm as TraineeUpdateLevelTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateNeedsTForm as TraineeUpdateNeedsTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateOrigineTForm as TraineeUpdateOrigineTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdatePhoneTForm as TraineeUpdatePhoneTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee\UpdateTownTForm as TraineeUpdateTownTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\AddTForm as TraineeHistoricalAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\AddTForm as TraineeRecordAddTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TraineeController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'admin');
        $this->addTwigVar('admmenu_active', 'trainees');
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $trainees = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->findAll();
        $this->addTwigVar('trainees', $trainees);

        $this->addTwigVar('admmenu_active', 'trainees_list');
        $this->addTwigVar('pageTitle', $this->translate('Trainee.pageTitle.admin.list'));
        $this->setHtmlHeadPageTitle($this->translate('Trainee.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Trainee:list.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportAction(Request $request)
    {
        $em = $this->getEntityManager();
        $trainees = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->findAll();

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $phpExcelObject->getProperties()
            ->setCreator('Salah Abdelkader Seif Eddine')
            ->setLastModifiedBy($this->getSecurityTokenStorage()
            ->getToken()
            ->getUser()
            ->getFullname())
            ->setTitle('Work Records Trainees List')
            ->setSubject('Work Records Trainees List')
            ->setDescription('Work Records Trainees List')
            ->setKeywords('Work Records Trainees List')
            ->setCategory('ILCFrance');
        $phpExcelObject->setActiveSheetIndex(0);

        $workSheet = $phpExcelObject->getActiveSheet();
        $workSheet->setTitle('Work Records Trainees List');

        $workSheet->setCellValue('A1', $this->translate('Trainee.lastName'));
        $workSheet->getStyle('A1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('B1', $this->translate('Trainee.firstName'));
        $workSheet->getStyle('B1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('E1', $this->translate('Trainee.email'));
        $workSheet->getStyle('E1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('F1', $this->translate('Trainee.phone'));
        $workSheet->getStyle('F1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('C1', $this->translate('Trainee.address'));
        $workSheet->getStyle('C1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('D1', $this->translate('Trainee.town'));
        $workSheet->getStyle('D1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('G1', $this->translate('Trainee.mobile'));
        $workSheet->getStyle('G1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('H1', $this->translate('Trainee.job'));
        $workSheet->getStyle('H1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('I1', $this->translate('Trainee.initLevel'));
        $workSheet->getStyle('I1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('J1', $this->translate('Trainee.level'));
        $workSheet->getStyle('J1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('K1', $this->translate('Trainee.needs'));
        $workSheet->getStyle('K1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('L1', $this->translate('Trainee.courses'));
        $workSheet->getStyle('L1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('M1', $this->translate('Trainee.origine'));
        $workSheet->getStyle('M1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('N1', $this->translate('TraineeHistorical.year'));
        $workSheet->getStyle('N1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('O1', $this->translate('TraineeHistorical.origine'));
        $workSheet->getStyle('O1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('P1', $this->translate('TraineeHistorical.initLevel'));
        $workSheet->getStyle('P1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('Q1', $this->translate('TraineeHistorical.level'));
        $workSheet->getStyle('Q1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('R1', $this->translate('TraineeHistorical.needs'));
        $workSheet->getStyle('R1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('S1', $this->translate('TraineeHistorical.courses'));
        $workSheet->getStyle('S1')
            ->getFont()
            ->setBold(true);
        $workSheet->setCellValue('T1', $this->translate('TraineeHistorical.lockout'));
        $workSheet->getStyle('T1')
            ->getFont()
            ->setBold(true);

        $workSheet->getStyle('A1:M1')->applyFromArray(array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '94CCDF'
                )
            )
        ));

        $workSheet->getStyle('N1:T1')->applyFromArray(array(
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '55A7C3'
                )
            )
        ));

        $i = 1;

        foreach ($trainees as $trainee) {
            if ($trainee->getHistoricals()->count() == 0) {
                $i++;
                $workSheet->setCellValue('A' . $i, $trainee->getLastName(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('B' . $i, $trainee->getFirstName(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('C' . $i, $trainee->getAddress(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('D' . $i, $trainee->getTown(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('E' . $i, $trainee->getEmail(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('F' . $i, $trainee->getPhone(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('G' . $i, $trainee->getMobile(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('H' . $i, $trainee->getJob(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('I' . $i, $trainee->getInitLevel(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('J' . $i, $trainee->getLevel(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('K' . $i, $trainee->getNeeds(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('L' . $i, $trainee->getCourses(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                $workSheet->setCellValue('M' . $i, $trainee->getOrigine(), \PHPExcel_Cell_DataType::TYPE_STRING2);

                if ($i % 2 == 1) {
                    $workSheet->getStyle('A' . $i . ':M' . $i)->applyFromArray(array(
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => 'd8f1f5'
                            )
                        )
                    ));
                } else {
                    $workSheet->getStyle('A' . $i . ':M' . $i)->applyFromArray(array(
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => 'bfbfbf'
                            )
                        )
                    ));
                }
            } else {
                foreach ($trainee->getHistoricals() as $historical) {
                    $i++;
                    $workSheet->setCellValue('A' . $i, $trainee->getLastName(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('B' . $i, $trainee->getFirstName(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('C' . $i, $trainee->getAddress(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('D' . $i, $trainee->getTown(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('E' . $i, $trainee->getEmail(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('F' . $i, $trainee->getPhone(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('G' . $i, $trainee->getMobile(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('H' . $i, $trainee->getJob(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('I' . $i, $trainee->getInitLevel(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('J' . $i, $trainee->getLevel(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('K' . $i, $trainee->getNeeds(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('L' . $i, $trainee->getCourses(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('M' . $i, $trainee->getOrigine(), \PHPExcel_Cell_DataType::TYPE_STRING2);

                    $workSheet->setCellValue('N' . $i, $historical->getYear(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('O' . $i, $historical->getOrigine(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('P' . $i, $historical->getInitLevel(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('Q' . $i, $historical->getLevel(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('R' . $i, $historical->getNeeds(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    $workSheet->setCellValue('S' . $i, $historical->getCourses(), \PHPExcel_Cell_DataType::TYPE_STRING2);
                    if ($historical->getLockout() == TraineeHistorical::LOCKOUT_UNLOCKED) {
                        $workSheet->setCellValue('T' . $i, 'IN_PROGRESS', \PHPExcel_Cell_DataType::TYPE_STRING2);
                    } else {
                        $workSheet->setCellValue('T' . $i, 'FINISHED', \PHPExcel_Cell_DataType::TYPE_STRING2);
                    }

                    if ($i % 2 == 1) {
                        $workSheet->getStyle('A' . $i . ':M' . $i)->applyFromArray(array(
                            'fill' => array(
                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array(
                                    'rgb' => 'D8F1F5'
                                )
                            )
                        ));
                    } else {
                        $workSheet->getStyle('A' . $i . ':M' . $i)->applyFromArray(array(
                            'fill' => array(
                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array(
                                    'rgb' => 'BFBFBF'
                                )
                            )
                        ));
                    }
                    if ($i % 2 == 1) {
                        $workSheet->getStyle('N' . $i . ':T' . $i)->applyFromArray(array(
                            'fill' => array(
                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array(
                                    'rgb' => 'C9DCDF'
                                )
                            )
                        ));
                    } else {
                        $workSheet->getStyle('N' . $i . ':T' . $i)->applyFromArray(array(
                            'fill' => array(
                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array(
                                    'rgb' => 'BAE3E5'
                                )
                            )
                        ));
                    }
                }
            }
        }

        $workSheet->getColumnDimension('A')->setAutoSize(true);
        $workSheet->getColumnDimension('B')->setAutoSize(true);
        $workSheet->getColumnDimension('C')->setAutoSize(true);
        $workSheet->getColumnDimension('D')->setAutoSize(true);
        $workSheet->getColumnDimension('E')->setAutoSize(true);
        $workSheet->getColumnDimension('F')->setAutoSize(true);
        $workSheet->getColumnDimension('G')->setAutoSize(true);
        $workSheet->getColumnDimension('H')->setAutoSize(true);
        $workSheet->getColumnDimension('I')->setAutoSize(true);
        $workSheet->getColumnDimension('J')->setAutoSize(true);
        $workSheet->getColumnDimension('K')->setAutoSize(true);
        $workSheet->getColumnDimension('L')->setAutoSize(true);
        $workSheet->getColumnDimension('M')->setAutoSize(true);
        $workSheet->getColumnDimension('N')->setAutoSize(true);
        $workSheet->getColumnDimension('O')->setAutoSize(true);
        $workSheet->getColumnDimension('P')->setAutoSize(true);
        $workSheet->getColumnDimension('Q')->setAutoSize(true);
        $workSheet->getColumnDimension('R')->setAutoSize(true);
        $workSheet->getColumnDimension('S')->setAutoSize(true);
        $workSheet->getColumnDimension('T')->setAutoSize(true);

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        $response = $this->get('phpexcel')->createStreamedResponse($writer);

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');

        $filename = $this->normalize('Work Records Trainees List');
        $filename = str_ireplace('"', '|', $filename);
        $filename = str_ireplace(' ', '_', $filename);

        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename . '.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addGetAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
        }
        $trainee = new Trainee();
        $traineeAddForm = $this->createForm(TraineeAddTForm::class, $trainee);
        $this->addTwigVar('trainee', $trainee);
        $this->addTwigVar('TraineeAddForm', $traineeAddForm->createView());

        $this->addTwigVar('admmenu_active', 'trainees_add');
        $this->addTwigVar('pageTitle', $this->translate('Trainee.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Trainee.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Trainee:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPostAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_addGet'));
        }
        $trainee = new Trainee();
        $traineeAddForm = $this->createForm(TraineeAddTForm::class, $trainee);

        $reqData = $request->request->all();

        if (isset($reqData['TraineeAddForm'])) {
            $traineeAddForm->handleRequest($request);
            if ($traineeAddForm->isValid()) {
                $em = $this->getEntityManager();
                $em->persist($trainee);
                $em->flush();

                $this->addFlash('success', $this->translate('Trainee.add.success', array(
                    '%trainee%' => $trainee->getFullName()
                )));

                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_editGet', array(
                    'id' => $trainee->getId()
                )));
            } else {
                $this->addFlash('error', $this->translate('Trainee.add.failure'));
            }
        }
        $this->addTwigVar('trainee', $trainee);
        $this->addTwigVar('TraineeAddForm', $traineeAddForm->createView());

        $this->addTwigVar('admmenu_active', 'trainees_add');
        $this->addTwigVar('pageTitle', $this->translate('Trainee.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Trainee.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Trainee:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function importGetAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }
        $traineeImportForm = $this->createForm(TraineeImportTForm::class);

        $this->addTwigVar('TraineeImportForm', $traineeImportForm->createView());

        $this->addTwigVar('admmenu_active', 'trainees_import');
        $this->addTwigVar('pageTitle', $this->translate('Trainee.pageTitle.admin.import'));
        $this->setHtmlHeadPageTitle($this->translate('Trainee.htmlHeadPageTitle.admin.import') . ' - ' . $this->getParameter('sitename'));

        return $this->render('IlcfrancePassportstagiaireFrontBundle:Trainee:import.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importPostAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }
        $traineeImportForm = $this->createForm(TraineeImportTForm::class);

        $reqData = $request->request->all();

        if (isset($reqData['TraineeImportForm'])) {
            $traineeImportForm->handleRequest($request);
            if ($traineeImportForm->isValid()) {

                ini_set('memory_limit', '4096M');
                ini_set('max_execution_time', '0');

                $em = $this->getEntityManager();
                $dbTrainees = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->findAll();

                $extension = $traineeImportForm['excel']->getData()->guessExtension();
                if ($extension == 'zip') {
                    $extension = 'xlsx';
                }

                $filename = uniqid() . '.' . $extension;
                $traineeImportForm['excel']->getData()->move($this->getParameter('adapter_files'), $filename);
                $fullfilename = $this->getParameter('adapter_files');
                $fullfilename .= '/' . $filename;

                $excelObj = $this->get('phpexcel')->createPHPExcelObject($fullfilename);

                $log = '';

                $iterator = $excelObj->getWorksheetIterator();

                $activeSheetIndex = -1;
                $i = 0;

                foreach ($iterator as $worksheet) {
                    $worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

                    $log .= "Feuille : '" . $worksheetTitle . "' trouvée contenant " . $highestRow . ' lignes et ' . $highestColumnIndex . ' colonnes avec comme plus grand index ' . $highestColumn . ' <br>';
                    if (\trim($worksheetTitle) == 'Work Records Trainees List') {
                        $activeSheetIndex = $i;
                    }
                    $i++;
                }
                if ($activeSheetIndex == -1) {
                    $log .= "Aucune Feuille de Titre 'Trainees' trouvée tentative d'import depuis le première Feuille<br>";
                    $activeSheetIndex = 0;
                }

                $excelObj->setActiveSheetIndex($activeSheetIndex);
                $worksheet = $excelObj->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                $lineRead = 0;
                $lineUnprocessed = 0;
                $traineeNew = 0;
                $histNew = 0;
                $lineError = 0;

                for ($row = 2; $row <= $highestRow; $row++) {
                    $lineRead++;

                    $lastName = \strtolower(\trim(\strval($worksheet->getCellByColumnAndRow(0, $row)->getValue())));
                    $firstName = \strtolower(\trim(\strval($worksheet->getCellByColumnAndRow(1, $row)->getValue())));
                    $address = \strtolower(\trim(\strval($worksheet->getCellByColumnAndRow(2, $row)->getValue())));
                    $town = \trim(\strval($worksheet->getCellByColumnAndRow(3, $row)->getValue()));
                    $email = \trim(\strval($worksheet->getCellByColumnAndRow(4, $row)->getValue()));
                    $phone = \trim(\strval($worksheet->getCellByColumnAndRow(5, $row)->getValue()));
                    $mobile = \trim(\strval($worksheet->getCellByColumnAndRow(6, $row)->getValue()));
                    $job = \trim(\strval($worksheet->getCellByColumnAndRow(7, $row)->getValue()));
                    $initLevel = \trim(\strval($worksheet->getCellByColumnAndRow(8, $row)->getValue()));
                    $level = \trim(\strval($worksheet->getCellByColumnAndRow(9, $row)->getValue()));
                    $needs = \trim(\strval($worksheet->getCellByColumnAndRow(10, $row)->getValue()));
                    $courses = \trim(\strval($worksheet->getCellByColumnAndRow(11, $row)->getValue()));
                    $origine = \trim(\strval($worksheet->getCellByColumnAndRow(12, $row)->getValue()));

                    $histYear = \trim(\strval($worksheet->getCellByColumnAndRow(13, $row)->getValue()));
                    $histOrigine = \trim(\strval($worksheet->getCellByColumnAndRow(14, $row)->getValue()));
                    $histInitLevel = \trim(\strval($worksheet->getCellByColumnAndRow(15, $row)->getValue()));
                    $histLevel = \trim(\strval($worksheet->getCellByColumnAndRow(16, $row)->getValue()));
                    $histNeeds = \trim(\strval($worksheet->getCellByColumnAndRow(17, $row)->getValue()));
                    $histCourses = \trim(\strval($worksheet->getCellByColumnAndRow(18, $row)->getValue()));
                    $histLockout = \trim(\strval($worksheet->getCellByColumnAndRow(19, $row)->getValue()));

                    if ($origine == '') {
                        $origine = 'PASSPORT';
                    }

                    if ($lastName != '' && $firstName != '') {
                        $trainee = null;
                        $hist = null;
                        foreach ($dbTrainees as $dbTrainee) {
                            if ($dbTrainee->getLastName() == $lastName && $dbTrainee->getFirstName() == $firstName) {
                                $trainee = $dbTrainee;
                            }
                        }
                        // $trainee = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->findOneBy(array(
                        // 'lastName' => $lastName,
                        // 'firstName' => $firstName
                        // ));
                        if (null == $trainee) {
                            $traineeNew++;
                            $histNew++;

                            $trainee = new Trainee();
                            $trainee->setFirstName($firstName);
                            $trainee->setLastName($lastName);
                            $trainee->setAddress($address);
                            $trainee->setTown($town);
                            $trainee->setEmail($email);
                            $trainee->setPhone($phone);
                            $trainee->setMobile($mobile);
                            $trainee->setJob($job);
                            $trainee->setInitLevel($initLevel);
                            $trainee->setLevel($level);
                            $trainee->setNeeds($needs);
                            $trainee->setCourses($courses);
                            $trainee->setOrigine($origine);

                            $em->persist($trainee);
                            $log .= 'L' . $row . ": le Stagiaire " . $lastName . ' ' . $firstName . ' est nouveau. ';

                            if (\trim($histYear) == '' || !\is_int($histYear)) {
                                $histYear = \date('Y');
                            }
                            if (\trim($histOrigine) == '') {
                                $histOrigine = $origine;
                            }
                            if (\trim($histInitLevel) == '') {
                                $histInitLevel = $initLevel;
                            }
                            if (\trim($histLevel) == '') {
                                $histLevel = $level;
                            }
                            if (\trim($histNeeds) == '') {
                                $histNeeds = $needs;
                            }
                            if (\trim($histCourses) == '') {
                                $histCourses = $courses;
                            }

                            if (\strtolower(\trim($histLockout)) == 'in_progress' || \strtolower(\trim($histLockout)) == 'no' || \strtolower(\trim($histLockout)) == \strtolower(TraineeHistorical::LOCKOUT_UNLOCKED)) {
                                $histLockout = TraineeHistorical::LOCKOUT_UNLOCKED;
                            } elseif (\strtolower(\trim($histLockout)) == 'finished' || \strtolower(\trim($histLockout)) == 'yes' || \strtolower(\trim($histLockout)) == \strtolower(TraineeHistorical::LOCKOUT_LOCKED)) {
                                $histLockout = TraineeHistorical::LOCKOUT_LOCKED;
                            } else {
                                $histLockout = '';
                            }

                            $hist = new TraineeHistorical();
                            $hist->setTrainee($trainee);
                            $hist->setYear($histYear);
                            $hist->setOrigine($histOrigine);
                            $hist->setInitLevel($histInitLevel);
                            $hist->setLevel($histLevel);
                            $hist->setNeeds($histNeeds);
                            $hist->setCourses($histCourses);
                            if ($histLockout != '') {
                                $hist->setLockout($histLockout);
                            }

                            $em->persist($hist);
                            $log .= "Nouveau Parcours " . $histYear . ' ' . $histOrigine . '.<br>';

                            $trainee->addHistorical($hist);
                            $em->persist($trainee);
                            $dbTrainees[] = $trainee;
                        } else {
                            $updateTrainee = false;
                            if (\trim($address) != "" && $trainee->getAddress() != $address) {
                                $trainee->setAddress($address);
                                $updateTrainee = true;
                            }
                            if (\trim($town) != "" && $trainee->getTown() != $town) {
                                $trainee->setTown($town);
                                $updateTrainee = true;
                            }
                            if (\trim($email) != "" && $trainee->getEmail() != $email) {
                                $trainee->setEmail($email);
                                $updateTrainee = true;
                            }
                            if (\trim($phone) != "" && $trainee->getPhone() != $phone) {
                                $trainee->setPhone($phone);
                                $updateTrainee = true;
                            }
                            if (\trim($mobile) != "" && $trainee->getMobile() != $mobile) {
                                $trainee->setMobile($mobile);
                                $updateTrainee = true;
                            }
                            if (\trim($job) != "" && $trainee->getJob() != $job) {
                                $trainee->setJob($job);
                                $updateTrainee = true;
                            }
                            if (\trim($initLevel) != "" && $trainee->getInitLevel() != $initLevel) {
                                $trainee->setInitLevel($initLevel);
                                $updateTrainee = true;
                            }
                            if (\trim($needs) != "" && $trainee->getNeeds() != $needs) {
                                $trainee->setNeeds($needs);
                                $updateTrainee = true;
                            }
                            if (\trim($courses) != "" && $trainee->getCourses() != $courses) {
                                $trainee->setCourses($courses);
                                $updateTrainee = true;
                            }
                            if (\trim($origine) != "" && $trainee->getOrigine() != $origine) {
                                $trainee->setOrigine($origine);
                                $updateTrainee = true;
                            }
                            if ($updateTrainee) {
                                $em->persist($trainee);
                                $log .= 'L' . $row . ": le Stagiaire " . $lastName . ' ' . $firstName . ' a été mis à jour. ';
                            } else {
                                // $log .= 'L'.$row.": le Stagiaire " . $lastName . ' ' . $firstName . ' existe déjà. ';
                            }
                            if (\trim($histYear) != '' && \trim($histOrigine) != '') {
                                foreach ($trainee->getHistoricals() as $dbHist) {
                                    if ($dbHist->getYear() == $histYear && $dbHist->getOrigine() == $histOrigine) {
                                        $hist = $dbHist;
                                    }
                                }
                                if (null != $hist) {

                                    $updateHist = false;
                                    if (\trim($histInitLevel) != "" && $hist->getInitLevel() != $histInitLevel) {
                                        $hist->setInitLevel($histInitLevel);
                                        $updateHist = true;
                                    }
                                    if (\trim($histLevel) != "" && $hist->getLevel() != $histLevel) {
                                        $hist->setLevel($histLevel);
                                        $updateHist = true;
                                    }
                                    if (\trim($histNeeds) != "" && $hist->getNeeds() != $histNeeds) {
                                        $hist->setNeeds($histNeeds);
                                        $updateHist = true;
                                    }
                                    if (\trim($histCourses) != "" && $hist->getCourses() != $histCourses) {
                                        $hist->setCourses($histCourses);
                                        $updateHist = true;
                                    }

                                    if (\strtolower(\trim($histLockout)) == 'in_progress' || \strtolower(\trim($histLockout)) == 'no' || \strtolower(\trim($histLockout)) == \strtolower(TraineeHistorical::LOCKOUT_UNLOCKED)) {
                                        $histLockout = TraineeHistorical::LOCKOUT_UNLOCKED;
                                        if ($hist->getLockout() != $histLockout) {
                                            $hist->setLockout($histLockout);
                                            $updateHist = true;
                                        }
                                    } elseif (\strtolower(\trim($histLockout)) == 'finished' || \strtolower(\trim($histLockout)) == 'yes' || \strtolower(\trim($histLockout)) == \strtolower(TraineeHistorical::LOCKOUT_LOCKED)) {
                                        $histLockout = TraineeHistorical::LOCKOUT_LOCKED;
                                        if ($hist->getLockout() != $histLockout) {
                                            $hist->setLockout($histLockout);
                                            $updateHist = true;
                                        }
                                    }

                                    if ($updateHist) {
                                        if (!$updateTrainee) {
                                            $log .= 'L' . $row . ': ';
                                        }
                                        $em->persist($hist);
                                        $log .= "le Parcours " . $histYear . ' ' . $histOrigine . ' a été mis à jour.<br>';
                                    } else {
                                        // $log .= "le Parcours " . $histYear . ' ' . $histOrigine . ' existe déjà.<br>';
                                        if ($updateTrainee) {
                                            $log .= '<br>';
                                        }
                                    }
                                } else {
                                    $histNew++;

                                    if (\strtolower(\trim($histLockout)) == 'in_progress' || \strtolower(\trim($histLockout)) == 'no' || \strtolower(\trim($histLockout)) == \strtolower(TraineeHistorical::LOCKOUT_UNLOCKED)) {
                                        $histLockout = TraineeHistorical::LOCKOUT_UNLOCKED;
                                    } elseif (\strtolower(\trim($histLockout)) == 'finished' || \strtolower(\trim($histLockout)) == 'yes' || \strtolower(\trim($histLockout)) == \strtolower(TraineeHistorical::LOCKOUT_LOCKED)) {
                                        $histLockout = TraineeHistorical::LOCKOUT_LOCKED;
                                    } else {
                                        $histLockout = '';
                                    }

                                    $hist = new TraineeHistorical();
                                    $hist->setTrainee($trainee);
                                    $hist->setYear($histYear);
                                    $hist->setOrigine($histOrigine);
                                    $hist->setInitLevel($histInitLevel);
                                    $hist->setLevel($histLevel);
                                    $hist->setNeeds($histNeeds);
                                    $hist->setCourses($histCourses);
                                    if ($histLockout != '') {
                                        $hist->setLockout($histLockout);
                                    }

                                    $em->persist($hist);

                                    $trainee->addHistorical($hist);
                                    $em->persist($trainee);
                                    if (!$updateTrainee) {
                                        $log .= 'L' . $row . ': ';
                                    }
                                    $log .= "Nouveau Parcours " . $histYear . ' ' . $histOrigine . '.<br>';
                                }
                            }
                            $lineUnprocessed++;
                        }
                    } else {
                        $lineError++;
                        $log .= 'la ligne ' . $row . ' contient des erreurs<br>';
                    }
                }
                $em->flush();

                $log .= $lineRead . ' lignes lues<br>';
                $log .= $traineeNew . " nouveaux Stagiaires<br>";
                $log .= $histNew . " nouveaux Parcours<br>";
                $log .= $lineUnprocessed . " Stagiaires déjà dans la base<br>";
                $log .= $lineError . ' lignes contenant des erreurs<br>';

                $this->addFlash('log', $log);

                $this->addFlash('success', $this->translate('Trainee.import.success'));

                return $this->redirect($urlFrom);
            }
        }

        $this->addTwigVar('TraineeImportForm', $traineeImportForm->createView());

        $this->addTwigVar('admmenu_active', 'trainees_import');
        $this->addTwigVar('pageTitle', $this->translate('Trainee.pageTitle.admin.import'));
        $this->setHtmlHeadPageTitle($this->translate('Trainee.htmlHeadPageTitle.admin.import') . ' - ' . $this->getParameter('sitename'));

        return $this->render('IlcfrancePassportstagiaireFrontBundle:Trainee:import.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }
        $em = $this->getEntityManager();
        try {
            $trainee = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->find($id);

            if (null == $trainee) {
                $this->addFlash('warning', $this->translate('Trainee.notfound'));
            } else {
                $em->remove($trainee);
                $em->flush();

                $this->addFlash('success', $this->translate('Trainee.delete.success', array(
                    '%trainee%' => $trainee->getFullName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('Trainee.delete.failure'));
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editGetAction($id, Request $request)
    {
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }

        $em = $this->getEntityManager();

        try {
            $trainee = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->find($id);

            if (null == $trainee) {
                $this->addFlash('warning', $this->translate('Trainee.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {

                $traineeUpdateAddressForm = $this->createForm(TraineeUpdateAddressTForm::class, $trainee);
                $traineeUpdateCoursesForm = $this->createForm(TraineeUpdateCoursesTForm::class, $trainee);
                $traineeUpdateEmailForm = $this->createForm(TraineeUpdateEmailTForm::class, $trainee);
                $traineeUpdateFirstNameForm = $this->createForm(TraineeUpdateFirstNameTForm::class, $trainee);
                $traineeUpdateInitLevelForm = $this->createForm(TraineeUpdateInitLevelTForm::class, $trainee);
                $traineeUpdateJobForm = $this->createForm(TraineeUpdateJobTForm::class, $trainee);
                $traineeUpdateLastNameForm = $this->createForm(TraineeUpdateLastNameTForm::class, $trainee);
                $traineeUpdateLevelForm = $this->createForm(TraineeUpdateLevelTForm::class, $trainee);
                $traineeUpdateNeedsForm = $this->createForm(TraineeUpdateNeedsTForm::class, $trainee);
                $traineeUpdateOrigineForm = $this->createForm(TraineeUpdateOrigineTForm::class, $trainee);
                $traineeUpdatePhoneForm = $this->createForm(TraineeUpdatePhoneTForm::class, $trainee);
                $traineeUpdateTownForm = $this->createForm(TraineeUpdateTownTForm::class, $trainee);

                $traineeHistorical = new TraineeHistorical();
                $traineeHistorical->setTrainee($trainee);
                $traineeHistorical->setOrigine($trainee->getOrigine());
                $traineeHistorical->setInitLevel($trainee->getInitLevel());
                $traineeHistorical->setLevel($trainee->getLevel());
                $traineeHistorical->setCourses($trainee->getCourses());
                $traineeHistorical->setNeeds($trainee->getNeeds());

                $traineeHistoricalAddForm = $this->createForm(TraineeHistoricalAddTForm::class, $traineeHistorical, array(
                    'trainee' => $trainee
                ));

                $traineeRecord = new TraineeRecord();
                $teacher = $this->getSecurityTokenStorage()
                    ->getToken()
                    ->getUser();
                $traineeRecord->setTeacher($teacher);
                $traineeRecord->setTeacherName($teacher->getFullName());
                $traineeRecord->setTrainee($trainee);
                $now = new \DateTime();
                $hour = \date('H');
                $now->setTime($hour, 0, 0);
                $traineeRecord->setRecordDate($now);

                $traineeRecordAddForm = $this->createForm(TraineeRecordAddTForm::class, $traineeRecord, array(
                    'trainee' => $trainee
                ));

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('trainee', $trainee);
                $this->addTwigVar('TraineeUpdateAddressForm', $traineeUpdateAddressForm->createView());
                $this->addTwigVar('TraineeUpdateCoursesForm', $traineeUpdateCoursesForm->createView());
                $this->addTwigVar('TraineeUpdateEmailForm', $traineeUpdateEmailForm->createView());
                $this->addTwigVar('TraineeUpdateFirstNameForm', $traineeUpdateFirstNameForm->createView());
                $this->addTwigVar('TraineeUpdateInitLevelForm', $traineeUpdateInitLevelForm->createView());
                $this->addTwigVar('TraineeUpdateJobForm', $traineeUpdateJobForm->createView());
                $this->addTwigVar('TraineeUpdateLastNameForm', $traineeUpdateLastNameForm->createView());
                $this->addTwigVar('TraineeUpdateLevelForm', $traineeUpdateLevelForm->createView());
                $this->addTwigVar('TraineeUpdateNeedsForm', $traineeUpdateNeedsForm->createView());
                $this->addTwigVar('TraineeUpdateOrigineForm', $traineeUpdateOrigineForm->createView());
                $this->addTwigVar('TraineeUpdatePhoneForm', $traineeUpdatePhoneForm->createView());
                $this->addTwigVar('TraineeUpdateTownForm', $traineeUpdateTownForm->createView());

                $this->addTwigVar('TraineeHistoricalAddForm', $traineeHistoricalAddForm->createView());
                $this->addTwigVar('TraineeRecordAddForm', $traineeRecordAddForm->createView());

                $this->addTwigVar('admmenu_active', 'trainees_edit');
                $this->addTwigVar('pageTitle', $this->translate('Trainee.pageTitle.admin.edit', array(
                    '%trainee%' => $trainee->getFullName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Trainee.htmlHeadPageTitle.admin.edit', array(
                    '%trainee%' => $trainee->getFullName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Trainee:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editPostAction($id, Request $request)
    {
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }

        $em = $this->getEntityManager();
        try {
            $trainee = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Trainee')->find($id);

            if (null == $trainee) {
                $this->addFlash('warning', $this->translate('Trainee.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {
                $traineeUpdateAddressForm = $this->createForm(TraineeUpdateAddressTForm::class, $trainee);
                $traineeUpdateCoursesForm = $this->createForm(TraineeUpdateCoursesTForm::class, $trainee);
                $traineeUpdateEmailForm = $this->createForm(TraineeUpdateEmailTForm::class, $trainee);
                $traineeUpdateFirstNameForm = $this->createForm(TraineeUpdateFirstNameTForm::class, $trainee);
                $traineeUpdateInitLevelForm = $this->createForm(TraineeUpdateInitLevelTForm::class, $trainee);
                $traineeUpdateJobForm = $this->createForm(TraineeUpdateJobTForm::class, $trainee);
                $traineeUpdateLastNameForm = $this->createForm(TraineeUpdateLastNameTForm::class, $trainee);
                $traineeUpdateLevelForm = $this->createForm(TraineeUpdateLevelTForm::class, $trainee);
                $traineeUpdateNeedsForm = $this->createForm(TraineeUpdateNeedsTForm::class, $trainee);
                $traineeUpdateOrigineForm = $this->createForm(TraineeUpdateOrigineTForm::class, $trainee);
                $traineeUpdatePhoneForm = $this->createForm(TraineeUpdatePhoneTForm::class, $trainee);
                $traineeUpdateTownForm = $this->createForm(TraineeUpdateTownTForm::class, $trainee);

                $traineeHistorical = new TraineeHistorical();
                $traineeHistorical->setTrainee($trainee);
                $traineeHistorical->setOrigine($trainee->getOrigine());
                $traineeHistorical->setInitLevel($trainee->getInitLevel());
                $traineeHistorical->setLevel($trainee->getLevel());
                $traineeHistorical->setCourses($trainee->getCourses());
                $traineeHistorical->setNeeds($trainee->getNeeds());

                $traineeHistoricalAddForm = $this->createForm(TraineeHistoricalAddTForm::class, $traineeHistorical, array(
                    'trainee' => $trainee
                ));

                $traineeRecord = new TraineeRecord();
                $teacher = $this->getSecurityTokenStorage()
                    ->getToken()
                    ->getUser();
                $traineeRecord->setTeacher($teacher);
                $traineeRecord->setTeacherName($teacher->getFullName());
                $traineeRecord->setTrainee($trainee);
                $now = new \DateTime();
                $hour = \date('H');
                $now->setTime($hour, 0, 0);
                $traineeRecord->setRecordDate($now);

                $traineeRecordAddForm = $this->createForm(TraineeRecordAddTForm::class, $traineeRecord);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['TraineeUpdateAddressForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateAddressForm->handleRequest($request);
                    if ($traineeUpdateAddressForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateCoursesForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateCoursesForm->handleRequest($request);
                    if ($traineeUpdateCoursesForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateEmailForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateEmailForm->handleRequest($request);
                    if ($traineeUpdateEmailForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateFirstNameForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateFirstNameForm->handleRequest($request);
                    if ($traineeUpdateFirstNameForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateJobForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateJobForm->handleRequest($request);
                    if ($traineeUpdateJobForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateInitLevelForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateInitLevelForm->handleRequest($request);
                    if ($traineeUpdateInitLevelForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateLastNameForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateLastNameForm->handleRequest($request);
                    if ($traineeUpdateLastNameForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateLevelForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateLevelForm->handleRequest($request);
                    if ($traineeUpdateLevelForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateNeedsForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateNeedsForm->handleRequest($request);
                    if ($traineeUpdateNeedsForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateOrigineForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateOrigineForm->handleRequest($request);
                    if ($traineeUpdateOrigineForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdatePhoneForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdatePhoneForm->handleRequest($request);
                    if ($traineeUpdatePhoneForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeUpdateTownForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeUpdateTownForm->handleRequest($request);
                    if ($traineeUpdateTownForm->isValid()) {
                        $em->persist($trainee);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Trainee.edit.success', array(
                            '%trainee%' => $trainee->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%trainee%' => $trainee->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalAddForm'])) {
                    $this->addTwigVar('tabActive', 3);
                    $this->getSession()->set('tabActive', 3);
                    $traineeHistoricalAddForm->handleRequest($request);
                    if ($traineeHistoricalAddForm->isValid()) {
                        $updateTrainee = $traineeHistoricalAddForm['traineeOverride']->getData();
                        if ($updateTrainee == TraineeHistorical::TRAINEE_OVERRIDE_YES) {
                            $trainee->setOrigine($traineeHistorical->getOrigine());
                            $trainee->setInitLevel($traineeHistorical->getInitLevel());
                            $trainee->setLevel($traineeHistorical->getLevel());
                            $trainee->setCourses($traineeHistorical->getCourses());
                            $trainee->setNeeds($traineeHistorical->getNeeds());

                            $em->persist($trainee);
                        }

                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.add.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                        $this->addTwigVar('tabActive', 1);
                        $this->getSession()->set('tabActive', 1);

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('TraineeHistorical.add.failure'));
                    }
                } elseif (isset($reqData['TraineeRecordAddForm'])) {
                    $this->addTwigVar('tabActive', 4);
                    $this->getSession()->set('tabActive', 4);
                    $traineeRecordAddForm->handleRequest($request);
                    if ($traineeRecordAddForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.add.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                        $this->addTwigVar('tabActive', 1);
                        $this->getSession()->set('tabActive', 1);

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($trainee);

                        $this->addFlash('error', $this->translate('TraineeRecord.add.failure'));
                    }
                }

                $this->addTwigVar('trainee', $trainee);
                $this->addTwigVar('TraineeUpdateAddressForm', $traineeUpdateAddressForm->createView());
                $this->addTwigVar('TraineeUpdateCoursesForm', $traineeUpdateCoursesForm->createView());
                $this->addTwigVar('TraineeUpdateEmailForm', $traineeUpdateEmailForm->createView());
                $this->addTwigVar('TraineeUpdateFirstNameForm', $traineeUpdateFirstNameForm->createView());
                $this->addTwigVar('TraineeUpdateInitLevelForm', $traineeUpdateInitLevelForm->createView());
                $this->addTwigVar('TraineeUpdateJobForm', $traineeUpdateJobForm->createView());
                $this->addTwigVar('TraineeUpdateLastNameForm', $traineeUpdateLastNameForm->createView());
                $this->addTwigVar('TraineeUpdateLevelForm', $traineeUpdateLevelForm->createView());
                $this->addTwigVar('TraineeUpdateNeedsForm', $traineeUpdateNeedsForm->createView());
                $this->addTwigVar('TraineeUpdateOrigineForm', $traineeUpdateOrigineForm->createView());
                $this->addTwigVar('TraineeUpdatePhoneForm', $traineeUpdatePhoneForm->createView());
                $this->addTwigVar('TraineeUpdateTownForm', $traineeUpdateTownForm->createView());

                $this->addTwigVar('TraineeHistoricalAddForm', $traineeHistoricalAddForm->createView());
                $this->addTwigVar('TraineeRecordAddForm', $traineeRecordAddForm->createView());

                $this->addTwigVar('admmenu_active', 'trainees_edit');
                $this->addTwigVar('pageTitle', $this->translate('Trainee.pageTitle.admin.edit', array(
                    '%trainee%' => $trainee->getFullName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Trainee.htmlHeadPageTitle.admin.edit', array(
                    '%trainee%' => $trainee->getFullName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Trainee:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}