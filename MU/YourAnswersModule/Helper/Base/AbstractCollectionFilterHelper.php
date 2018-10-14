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
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use MU\YourAnswersModule\Entity\AnswerEntity;
use MU\YourAnswersModule\Entity\QuestionEntity;
use MU\YourAnswersModule\Helper\PermissionHelper;

/**
 * Entity collection filter helper base class.
 */
abstract class AbstractCollectionFilterHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var bool Fallback value to determine whether only own entries should be selected or not
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * @var bool Whether to apply a locale-based filter or not
     */
    protected $filterDataByLocale = false;
    
    /**
     * CollectionFilterHelper constructor.
     *
     * @param RequestStack $requestStack RequestStack service instance
     * @param PermissionHelper $permissionHelper PermissionHelper service instance
     * @param CurrentUserApiInterface $currentUserApi CurrentUserApi service instance
     * @param boolean $showOnlyOwnEntries Fallback value to determine whether only own entries should be selected or not
     * @param boolean $filterDataByLocale Whether to apply a locale-based filter or not
     */
    public function __construct(
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi,
        $showOnlyOwnEntries,
        $filterDataByLocale
    ) {
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
        $this->showOnlyOwnEntries = $showOnlyOwnEntries;
        $this->filterDataByLocale = $filterDataByLocale;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $objectType Name of treated entity type
     * @param string $context    Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args       Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    public function getViewQuickNavParameters($objectType = '', $context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'])) {
            $context = 'controllerAction';
        }
    
        if ($objectType == 'answer') {
            return $this->getViewQuickNavParametersForAnswer($context, $args);
        }
        if ($objectType == 'question') {
            return $this->getViewQuickNavParametersForQuestion($context, $args);
        }
    
        return [];
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param string       $objectType Name of treated entity type
     * @param QueryBuilder $qb         Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCommonViewFilters($objectType, QueryBuilder $qb)
    {
        if ($objectType == 'answer') {
            return $this->addCommonViewFiltersForAnswer($qb);
        }
        if ($objectType == 'question') {
            return $this->addCommonViewFiltersForQuestion($qb);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param string       $objectType Name of treated entity type
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param array        $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function applyDefaultFilters($objectType, QueryBuilder $qb, array $parameters = [])
    {
        if ($objectType == 'answer') {
            return $this->applyDefaultFiltersForAnswer($qb, $parameters);
        }
        if ($objectType == 'question') {
            return $this->applyDefaultFiltersForQuestion($qb, $parameters);
        }
    
        return $qb;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args    Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForAnswer($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['question'] = $request->query->get('question', 0);
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['q'] = $request->query->get('q', '');
    
        return $parameters;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args    Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForQuestion($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['forLanguage'] = $request->query->get('forLanguage', '');
        $parameters['q'] = $request->query->get('q', '');
    
        return $parameters;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForAnswer(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForAnswer();
        foreach ($parameters as $k => $v) {
            if (in_array($k, ['q', 'searchterm'])) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('answer', $qb, $v);
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && $v != '') || (is_numeric($v) && $v > 0)) {
                if ($k == 'workflowState' && substr($v, 0, 1) == '!') {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1, strlen($v)-1));
                } elseif (substr($v, 0, 1) == '%') {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        $qb = $this->applyDefaultFiltersForAnswer($qb, $parameters);
    
        return $qb;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForQuestion(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForQuestion();
        foreach ($parameters as $k => $v) {
            if (in_array($k, ['q', 'searchterm'])) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('question', $qb, $v);
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && $v != '') || (is_numeric($v) && $v > 0)) {
                if ($k == 'workflowState' && substr($v, 0, 1) == '!') {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1, strlen($v)-1));
                } elseif (substr($v, 0, 1) == '%') {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        $qb = $this->applyDefaultFiltersForQuestion($qb, $parameters);
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param array        $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForAnswer(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        $isAdminArea = false !== strpos($routeName, 'muyouranswersmodule_answer_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
    
        if (!in_array('workflowState', array_keys($parameters)) || empty($parameters['workflowState'])) {
            // per default we show approved answers only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
        if (in_array('tblQuestion', $qb->getAllAliases())) {
            $qb = $this->applyDateRangeFilterForQuestion($qb, 'tblQuestion');
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param array        $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForQuestion(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        $isAdminArea = false !== strpos($routeName, 'muyouranswersmodule_question_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
    
        if (!in_array('workflowState', array_keys($parameters)) || empty($parameters['workflowState'])) {
            // per default we show approved questions only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        if (true === (bool)$this->filterDataByLocale) {
            $allowedLocales = ['', $request->getLocale()];
            if (!in_array('forLanguage', array_keys($parameters)) || empty($parameters['forLanguage'])) {
                $qb->andWhere('tbl.forLanguage IN (:currentForLanguage)')
                   ->setParameter('currentForLanguage', $allowedLocales);
            }
        }
    
        $qb = $this->applyDateRangeFilterForQuestion($qb);
    
        return $qb;
    }
    
    /**
     * Applies start and end date filters for selecting questions.
     *
     * @param QueryBuilder $qb    Query builder to be enhanced
     * @param string       $alias Table alias
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDateRangeFilterForQuestion(QueryBuilder $qb, $alias = 'tbl')
    {
        $request = $this->requestStack->getCurrentRequest();
        $startDate = $request->query->get('startDate', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.startDate <= :startDate OR ' . $alias . '.startDate IS NULL)')
           ->setParameter('startDate', $startDate);
    
        $endDate = $request->query->get('endDate', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.endDate >= :endDate OR ' . $alias . '.endDate IS NULL)')
           ->setParameter('endDate', $endDate);
    
        return $qb;
    }
    
    /**
     * Adds a where clause for search query.
     *
     * @param string       $objectType Name of treated entity type
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param string       $fragment   The fragment to search for
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addSearchFilter($objectType, QueryBuilder $qb, $fragment = '')
    {
        if ($fragment == '') {
            return $qb;
        }
    
        $filters = [];
        $parameters = [];
    
        if ($objectType == 'answer') {
            $filters[] = 'tbl.workflowState = :searchWorkflowState';
            $parameters['searchWorkflowState'] = $fragment;
            $filters[] = 'tbl.textOfAnswer LIKE :searchTextOfAnswer';
            $parameters['searchTextOfAnswer'] = '%' . $fragment . '%';
        }
        if ($objectType == 'question') {
            $filters[] = 'tbl.workflowState = :searchWorkflowState';
            $parameters['searchWorkflowState'] = $fragment;
            $filters[] = 'tbl.subject LIKE :searchSubject';
            $parameters['searchSubject'] = '%' . $fragment . '%';
            $filters[] = 'tbl.textOfQuestion LIKE :searchTextOfQuestion';
            $parameters['searchTextOfQuestion'] = '%' . $fragment . '%';
            $filters[] = 'tbl.forLanguage LIKE :searchForLanguage';
            $parameters['searchForLanguage'] = '%' . $fragment . '%';
            $filters[] = 'tbl.startDate = :searchStartDate';
            $parameters['searchStartDate'] = $fragment;
            $filters[] = 'tbl.endDate = :searchEndDate';
            $parameters['searchEndDate'] = $fragment;
        }
    
        $qb->andWhere('(' . implode(' OR ', $filters) . ')');
    
        foreach ($parameters as $parameterName => $parameterValue) {
            $qb->setParameter($parameterName, $parameterValue);
        }
    
        return $qb;
    }
    
    /**
     * Adds a filter for the createdBy field.
     *
     * @param QueryBuilder $qb     Query builder to be enhanced
     * @param integer      $userId The user identifier used for filtering
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCreatorFilter(QueryBuilder $qb, $userId = null)
    {
        if (null === $userId) {
            $userId = $this->currentUserApi->isLoggedIn() ? $this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        }
    
        if (is_array($userId)) {
            $qb->andWhere('tbl.createdBy IN (:userIds)')
               ->setParameter('userIds', $userId);
        } else {
            $qb->andWhere('tbl.createdBy = :userId')
               ->setParameter('userId', $userId);
        }
    
        return $qb;
    }
}
