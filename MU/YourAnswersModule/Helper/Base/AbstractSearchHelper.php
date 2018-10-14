<?php
/**
 * YourAnswers.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace MU\YourAnswersModule\Helper\Base;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Composite;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\RouteUrl;
use Zikula\SearchModule\Entity\SearchResultEntity;
use Zikula\SearchModule\SearchableInterface;
use MU\YourAnswersModule\Entity\Factory\EntityFactory;
use MU\YourAnswersModule\Helper\ControllerHelper;
use MU\YourAnswersModule\Helper\EntityDisplayHelper;
use MU\YourAnswersModule\Helper\PermissionHelper;

/**
 * Search helper base class.
 */
abstract class AbstractSearchHelper implements SearchableInterface
{
    use TranslatorTrait;
    
    /**
     * @var SessionInterface
     */
    protected $session;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * SearchHelper constructor.
     *
     * @param TranslatorInterface $translator          Translator service instance
     * @param SessionInterface    $session             Session service instance
     * @param RequestStack        $requestStack        RequestStack service instance
     * @param EntityFactory       $entityFactory       EntityFactory service instance
     * @param ControllerHelper    $controllerHelper    ControllerHelper service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     * @param PermissionHelper    $permissionHelper    PermissionHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        SessionInterface $session,
        RequestStack $requestStack,
        EntityFactory $entityFactory,
        ControllerHelper $controllerHelper,
        EntityDisplayHelper $entityDisplayHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->setTranslator($translator);
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->entityFactory = $entityFactory;
        $this->controllerHelper = $controllerHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->permissionHelper = $permissionHelper;
    }
    
    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * @inheritDoc
     */
    public function amendForm(FormBuilderInterface $builder)
    {
        if (!$this->permissionHelper->hasPermission(ACCESS_READ)) {
            return;
        }
    
        $builder->add('active', HiddenType::class, [
            'data' => true
        ]);
    
        $searchTypes = $this->getSearchTypes();
    
        foreach ($searchTypes as $searchType => $typeInfo) {
            $builder->add('active_' . $searchType, CheckboxType::class, [
                'value' => $typeInfo['value'],
                'label' => $typeInfo['label'],
                'label_attr' => ['class' => 'checkbox-inline'],
                'required' => false
            ]);
        }
    }
    
    /**
     * @inheritDoc
     */
    public function getResults(array $words, $searchType = 'AND', $modVars = null)
    {
        if (!$this->permissionHelper->hasPermission(ACCESS_READ)) {
            return [];
        }
    
        // initialise array for results
        $results = [];
    
        // retrieve list of activated object types
        $searchTypes = $this->getSearchTypes();
        $entitiesWithDisplayAction = ['answer', 'question'];
        $request = $this->requestStack->getCurrentRequest();
    
        foreach ($searchTypes as $searchTypeCode => $typeInfo) {
            $isActivated = false;
            $searchSettings = $request->query->get('zikulasearchmodule_search', []);
            $moduleActivationInfo = $searchSettings['modules'];
            if (isset($moduleActivationInfo['MUYourAnswersModule'])) {
                $moduleActivationInfo = $moduleActivationInfo['MUYourAnswersModule'];
                $isActivated = isset($moduleActivationInfo['active_' . $searchTypeCode]);
            }
            if (!$isActivated) {
                continue;
            }
    
            $objectType = $typeInfo['value'];
            $whereArray = [];
            $languageField = null;
            switch ($objectType) {
                case 'answer':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.name';
                    $whereArray[] = 'tbl.textOfAnswer';
                    break;
                case 'question':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.subject';
                    $whereArray[] = 'tbl.textOfQuestion';
                    $whereArray[] = 'tbl.forLanguage';
                    break;
            }
    
            $repository = $this->entityFactory->getRepository($objectType);
    
            // build the search query without any joins
            $qb = $repository->getListQueryBuilder('', '', false);
    
            // build where expression for given search type
            $whereExpr = $this->formatWhere($qb, $words, $whereArray, $searchType);
            $qb->andWhere($whereExpr);
    
            $query = $repository->getQueryFromBuilder($qb);
    
            // set a sensitive limit
            $query->setFirstResult(0)
                  ->setMaxResults(250);
    
            // fetch the results
            $entities = $query->getResult();
    
            if (count($entities) == 0) {
                continue;
            }
    
            $descriptionFieldName = $this->entityDisplayHelper->getDescriptionFieldName($objectType);
            $hasDisplayAction = in_array($objectType, $entitiesWithDisplayAction);
    
            foreach ($entities as $entity) {
                if (!$this->permissionHelper->mayRead($entity)) {
                    continue;
                }
    
                $description = !empty($descriptionFieldName) ? $entity[$descriptionFieldName] : '';
                $created = isset($entity['createdDate']) ? $entity['createdDate'] : null;
    
                $formattedTitle = $this->entityDisplayHelper->getFormattedTitle($entity);
                $displayUrl = '';
                if ($hasDisplayAction) {
                    $urlArgs = $entity->createUrlArgs();
                    $urlArgs['_locale'] = (null !== $languageField && !empty($entity[$languageField])) ? $entity[$languageField] : $request->getLocale();
                    $displayUrl = new RouteUrl('muyouranswersmodule_' . strtolower($objectType) . '_display', $urlArgs);
                }
    
                $result = new SearchResultEntity();
                $result->setTitle($formattedTitle)
                    ->setText($description)
                    ->setModule('MUYourAnswersModule')
                    ->setCreated($created)
                    ->setSesid($this->session->getId())
                    ->setUrl($displayUrl);
                $results[] = $result;
            }
        }
    
        return $results;
    }
    
    /**
     * Returns list of supported search types.
     *
     * @return array List of search types
     */
    protected function getSearchTypes()
    {
        $searchTypes = [
            'mUYourAnswersModuleAnswers' => [
                'value' => 'answer',
                'label' => $this->__('Answers')
            ],
            'mUYourAnswersModuleQuestions' => [
                'value' => 'question',
                'label' => $this->__('Questions')
            ]
        ];
    
        $allowedTypes = $this->controllerHelper->getObjectTypes('helper', ['helper' => 'search', 'action' => 'getSearchTypes']);
        $allowedSearchTypes = [];
        foreach ($searchTypes as $searchType => $typeInfo) {
            if (!in_array($typeInfo['value'], $allowedTypes)) {
                continue;
            }
            $allowedSearchTypes[$searchType] = $typeInfo;
        }
    
        return $allowedSearchTypes;
    }
    
    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        return [];
    }
    
    /**
     * Construct a QueryBuilder Where orX|andX Expr instance.
     *
     * @param QueryBuilder $qb
     * @param string[] $words  List of words to query for
     * @param string[] $fields List of fields to include into query
     * @param string $searchtype AND|OR|EXACT
     *
     * @return null|Composite
     */
    protected function formatWhere(QueryBuilder $qb, array $words = [], array $fields = [], $searchtype = 'AND')
    {
        if (empty($words) || empty($fields)) {
            return null;
        }
    
        $method = ($searchtype == 'OR') ? 'orX' : 'andX';
        /** @var $where Composite */
        $where = $qb->expr()->$method();
        $i = 1;
        foreach ($words as $word) {
            $subWhere = $qb->expr()->orX();
            foreach ($fields as $field) {
                $expr = $qb->expr()->like($field, "?$i");
                $subWhere->add($expr);
                $qb->setParameter($i, '%' . $word . '%');
                $i++;
            }
            $where->add($subWhere);
        }
    
        return $where;
    }
}
