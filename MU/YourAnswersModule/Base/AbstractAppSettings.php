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

namespace MU\YourAnswersModule\Base;

use Symfony\Component\Validator\Constraints as Assert;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\GroupsModule\Constant as GroupsConstant;
use Zikula\GroupsModule\Entity\RepositoryInterface\GroupRepositoryInterface;
use MU\YourAnswersModule\Validator\Constraints as YourAnswersAssert;

/**
 * Application settings class for handling module variables.
 */
abstract class AbstractAppSettings
{
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    
    /**
     * The amount of answers shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $answerEntriesPerPage
     */
    protected $answerEntriesPerPage = 10;
    
    /**
     * Whether to add a link to answers of the current user on his account page
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $linkOwnAnswersOnAccountPage
     */
    protected $linkOwnAnswersOnAccountPage = true;
    
    /**
     * The amount of questions shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $questionEntriesPerPage
     */
    protected $questionEntriesPerPage = 10;
    
    /**
     * Whether to add a link to questions of the current user on his account page
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $linkOwnQuestionsOnAccountPage
     */
    protected $linkOwnQuestionsOnAccountPage = true;
    
    /**
     * Whether only own entries should be shown on view pages by default or not
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $showOnlyOwnEntries
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * Whether automatically filter data in the frontend based on the current locale or not
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $filterDataByLocale
     */
    protected $filterDataByLocale = false;
    
    /**
     * Used to determine moderator user accounts for sending email notifications.
     *
     * @Assert\NotBlank()
     * @var integer $moderationGroupForAnswers
     */
    protected $moderationGroupForAnswers = 2;
    
    /**
     * Used to determine moderator user accounts for sending email notifications.
     *
     * @Assert\NotBlank()
     * @var integer $moderationGroupForQuestions
     */
    protected $moderationGroupForQuestions = 2;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreatorForAnswer
     */
    protected $allowModerationSpecificCreatorForAnswer = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreationDateForAnswer
     */
    protected $allowModerationSpecificCreationDateForAnswer = false;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreatorForQuestion
     */
    protected $allowModerationSpecificCreatorForQuestion = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreationDateForQuestion
     */
    protected $allowModerationSpecificCreationDateForQuestion = false;
    
    /**
     * Which sections are supported in the Finder component (used by Scribite plug-ins).
     *
     * @Assert\NotNull()
     * @YourAnswersAssert\ListEntry(entityName="appSettings", propertyName="enabledFinderTypes", multiple=true)
     * @var string $enabledFinderTypes
     */
    protected $enabledFinderTypes = 'answer###question';
    
    
    /**
     * AppSettings constructor.
     *
     * @param VariableApiInterface $variableApi VariableApi service instance
     * @param GroupRepositoryInterface $groupRepository GroupRepository service instance
     */
    public function __construct(
        VariableApiInterface $variableApi,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->variableApi = $variableApi;
        $this->groupRepository = $groupRepository;
    
        $this->load();
    }
    
    /**
     * Returns the answer entries per page.
     *
     * @return integer
     */
    public function getAnswerEntriesPerPage()
    {
        return $this->answerEntriesPerPage;
    }
    
    /**
     * Sets the answer entries per page.
     *
     * @param integer $answerEntriesPerPage
     *
     * @return void
     */
    public function setAnswerEntriesPerPage($answerEntriesPerPage)
    {
        if (intval($this->answerEntriesPerPage) !== intval($answerEntriesPerPage)) {
            $this->answerEntriesPerPage = intval($answerEntriesPerPage);
        }
    }
    
    /**
     * Returns the link own answers on account page.
     *
     * @return boolean
     */
    public function getLinkOwnAnswersOnAccountPage()
    {
        return $this->linkOwnAnswersOnAccountPage;
    }
    
    /**
     * Sets the link own answers on account page.
     *
     * @param boolean $linkOwnAnswersOnAccountPage
     *
     * @return void
     */
    public function setLinkOwnAnswersOnAccountPage($linkOwnAnswersOnAccountPage)
    {
        if (boolval($this->linkOwnAnswersOnAccountPage) !== boolval($linkOwnAnswersOnAccountPage)) {
            $this->linkOwnAnswersOnAccountPage = boolval($linkOwnAnswersOnAccountPage);
        }
    }
    
    /**
     * Returns the question entries per page.
     *
     * @return integer
     */
    public function getQuestionEntriesPerPage()
    {
        return $this->questionEntriesPerPage;
    }
    
    /**
     * Sets the question entries per page.
     *
     * @param integer $questionEntriesPerPage
     *
     * @return void
     */
    public function setQuestionEntriesPerPage($questionEntriesPerPage)
    {
        if (intval($this->questionEntriesPerPage) !== intval($questionEntriesPerPage)) {
            $this->questionEntriesPerPage = intval($questionEntriesPerPage);
        }
    }
    
    /**
     * Returns the link own questions on account page.
     *
     * @return boolean
     */
    public function getLinkOwnQuestionsOnAccountPage()
    {
        return $this->linkOwnQuestionsOnAccountPage;
    }
    
    /**
     * Sets the link own questions on account page.
     *
     * @param boolean $linkOwnQuestionsOnAccountPage
     *
     * @return void
     */
    public function setLinkOwnQuestionsOnAccountPage($linkOwnQuestionsOnAccountPage)
    {
        if (boolval($this->linkOwnQuestionsOnAccountPage) !== boolval($linkOwnQuestionsOnAccountPage)) {
            $this->linkOwnQuestionsOnAccountPage = boolval($linkOwnQuestionsOnAccountPage);
        }
    }
    
    /**
     * Returns the show only own entries.
     *
     * @return boolean
     */
    public function getShowOnlyOwnEntries()
    {
        return $this->showOnlyOwnEntries;
    }
    
    /**
     * Sets the show only own entries.
     *
     * @param boolean $showOnlyOwnEntries
     *
     * @return void
     */
    public function setShowOnlyOwnEntries($showOnlyOwnEntries)
    {
        if (boolval($this->showOnlyOwnEntries) !== boolval($showOnlyOwnEntries)) {
            $this->showOnlyOwnEntries = boolval($showOnlyOwnEntries);
        }
    }
    
    /**
     * Returns the filter data by locale.
     *
     * @return boolean
     */
    public function getFilterDataByLocale()
    {
        return $this->filterDataByLocale;
    }
    
    /**
     * Sets the filter data by locale.
     *
     * @param boolean $filterDataByLocale
     *
     * @return void
     */
    public function setFilterDataByLocale($filterDataByLocale)
    {
        if (boolval($this->filterDataByLocale) !== boolval($filterDataByLocale)) {
            $this->filterDataByLocale = boolval($filterDataByLocale);
        }
    }
    
    /**
     * Returns the moderation group for answers.
     *
     * @return integer
     */
    public function getModerationGroupForAnswers()
    {
        return $this->moderationGroupForAnswers;
    }
    
    /**
     * Sets the moderation group for answers.
     *
     * @param integer $moderationGroupForAnswers
     *
     * @return void
     */
    public function setModerationGroupForAnswers($moderationGroupForAnswers)
    {
        if ($this->moderationGroupForAnswers !== $moderationGroupForAnswers) {
            $this->moderationGroupForAnswers = $moderationGroupForAnswers;
        }
    }
    
    /**
     * Returns the moderation group for questions.
     *
     * @return integer
     */
    public function getModerationGroupForQuestions()
    {
        return $this->moderationGroupForQuestions;
    }
    
    /**
     * Sets the moderation group for questions.
     *
     * @param integer $moderationGroupForQuestions
     *
     * @return void
     */
    public function setModerationGroupForQuestions($moderationGroupForQuestions)
    {
        if ($this->moderationGroupForQuestions !== $moderationGroupForQuestions) {
            $this->moderationGroupForQuestions = $moderationGroupForQuestions;
        }
    }
    
    /**
     * Returns the allow moderation specific creator for answer.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreatorForAnswer()
    {
        return $this->allowModerationSpecificCreatorForAnswer;
    }
    
    /**
     * Sets the allow moderation specific creator for answer.
     *
     * @param boolean $allowModerationSpecificCreatorForAnswer
     *
     * @return void
     */
    public function setAllowModerationSpecificCreatorForAnswer($allowModerationSpecificCreatorForAnswer)
    {
        if (boolval($this->allowModerationSpecificCreatorForAnswer) !== boolval($allowModerationSpecificCreatorForAnswer)) {
            $this->allowModerationSpecificCreatorForAnswer = boolval($allowModerationSpecificCreatorForAnswer);
        }
    }
    
    /**
     * Returns the allow moderation specific creation date for answer.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreationDateForAnswer()
    {
        return $this->allowModerationSpecificCreationDateForAnswer;
    }
    
    /**
     * Sets the allow moderation specific creation date for answer.
     *
     * @param boolean $allowModerationSpecificCreationDateForAnswer
     *
     * @return void
     */
    public function setAllowModerationSpecificCreationDateForAnswer($allowModerationSpecificCreationDateForAnswer)
    {
        if (boolval($this->allowModerationSpecificCreationDateForAnswer) !== boolval($allowModerationSpecificCreationDateForAnswer)) {
            $this->allowModerationSpecificCreationDateForAnswer = boolval($allowModerationSpecificCreationDateForAnswer);
        }
    }
    
    /**
     * Returns the allow moderation specific creator for question.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreatorForQuestion()
    {
        return $this->allowModerationSpecificCreatorForQuestion;
    }
    
    /**
     * Sets the allow moderation specific creator for question.
     *
     * @param boolean $allowModerationSpecificCreatorForQuestion
     *
     * @return void
     */
    public function setAllowModerationSpecificCreatorForQuestion($allowModerationSpecificCreatorForQuestion)
    {
        if (boolval($this->allowModerationSpecificCreatorForQuestion) !== boolval($allowModerationSpecificCreatorForQuestion)) {
            $this->allowModerationSpecificCreatorForQuestion = boolval($allowModerationSpecificCreatorForQuestion);
        }
    }
    
    /**
     * Returns the allow moderation specific creation date for question.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreationDateForQuestion()
    {
        return $this->allowModerationSpecificCreationDateForQuestion;
    }
    
    /**
     * Sets the allow moderation specific creation date for question.
     *
     * @param boolean $allowModerationSpecificCreationDateForQuestion
     *
     * @return void
     */
    public function setAllowModerationSpecificCreationDateForQuestion($allowModerationSpecificCreationDateForQuestion)
    {
        if (boolval($this->allowModerationSpecificCreationDateForQuestion) !== boolval($allowModerationSpecificCreationDateForQuestion)) {
            $this->allowModerationSpecificCreationDateForQuestion = boolval($allowModerationSpecificCreationDateForQuestion);
        }
    }
    
    /**
     * Returns the enabled finder types.
     *
     * @return string
     */
    public function getEnabledFinderTypes()
    {
        return $this->enabledFinderTypes;
    }
    
    /**
     * Sets the enabled finder types.
     *
     * @param string $enabledFinderTypes
     *
     * @return void
     */
    public function setEnabledFinderTypes($enabledFinderTypes)
    {
        if ($this->enabledFinderTypes !== $enabledFinderTypes) {
            $this->enabledFinderTypes = isset($enabledFinderTypes) ? $enabledFinderTypes : '';
        }
    }
    
    
    /**
     * Loads module variables from the database.
     */
    protected function load()
    {
        $moduleVars = $this->variableApi->getAll('MUYourAnswersModule');
    
        if (isset($moduleVars['answerEntriesPerPage'])) {
            $this->setAnswerEntriesPerPage($moduleVars['answerEntriesPerPage']);
        }
        if (isset($moduleVars['linkOwnAnswersOnAccountPage'])) {
            $this->setLinkOwnAnswersOnAccountPage($moduleVars['linkOwnAnswersOnAccountPage']);
        }
        if (isset($moduleVars['questionEntriesPerPage'])) {
            $this->setQuestionEntriesPerPage($moduleVars['questionEntriesPerPage']);
        }
        if (isset($moduleVars['linkOwnQuestionsOnAccountPage'])) {
            $this->setLinkOwnQuestionsOnAccountPage($moduleVars['linkOwnQuestionsOnAccountPage']);
        }
        if (isset($moduleVars['showOnlyOwnEntries'])) {
            $this->setShowOnlyOwnEntries($moduleVars['showOnlyOwnEntries']);
        }
        if (isset($moduleVars['filterDataByLocale'])) {
            $this->setFilterDataByLocale($moduleVars['filterDataByLocale']);
        }
        if (isset($moduleVars['moderationGroupForAnswers'])) {
            $this->setModerationGroupForAnswers($moduleVars['moderationGroupForAnswers']);
        }
        if (isset($moduleVars['moderationGroupForQuestions'])) {
            $this->setModerationGroupForQuestions($moduleVars['moderationGroupForQuestions']);
        }
        if (isset($moduleVars['allowModerationSpecificCreatorForAnswer'])) {
            $this->setAllowModerationSpecificCreatorForAnswer($moduleVars['allowModerationSpecificCreatorForAnswer']);
        }
        if (isset($moduleVars['allowModerationSpecificCreationDateForAnswer'])) {
            $this->setAllowModerationSpecificCreationDateForAnswer($moduleVars['allowModerationSpecificCreationDateForAnswer']);
        }
        if (isset($moduleVars['allowModerationSpecificCreatorForQuestion'])) {
            $this->setAllowModerationSpecificCreatorForQuestion($moduleVars['allowModerationSpecificCreatorForQuestion']);
        }
        if (isset($moduleVars['allowModerationSpecificCreationDateForQuestion'])) {
            $this->setAllowModerationSpecificCreationDateForQuestion($moduleVars['allowModerationSpecificCreationDateForQuestion']);
        }
        if (isset($moduleVars['enabledFinderTypes'])) {
            $this->setEnabledFinderTypes($moduleVars['enabledFinderTypes']);
        }
    
        // prepare group selectors, fallback to admin group for undefined values
        $adminGroupId = GroupsConstant::GROUP_ID_ADMIN;
        $groupId = $this->getModerationGroupForAnswers();
        if ($groupId < 1) {
            $groupId = $adminGroupId;
        }
    
        $this->setModerationGroupForAnswers($this->groupRepository->find($groupId));
        $groupId = $this->getModerationGroupForQuestions();
        if ($groupId < 1) {
            $groupId = $adminGroupId;
        }
    
        $this->setModerationGroupForQuestions($this->groupRepository->find($groupId));
    }
    
    /**
     * Saves module variables into the database.
     */
    public function save()
    {
        // normalise group selector values
        $group = $this->getModerationGroupForAnswers();
        $group = is_object($group) ? $group->getGid() : intval($group);
        $this->setModerationGroupForAnswers($group);
        $group = $this->getModerationGroupForQuestions();
        $group = is_object($group) ? $group->getGid() : intval($group);
        $this->setModerationGroupForQuestions($group);
    
        $this->variableApi->set('MUYourAnswersModule', 'answerEntriesPerPage', $this->getAnswerEntriesPerPage());
        $this->variableApi->set('MUYourAnswersModule', 'linkOwnAnswersOnAccountPage', $this->getLinkOwnAnswersOnAccountPage());
        $this->variableApi->set('MUYourAnswersModule', 'questionEntriesPerPage', $this->getQuestionEntriesPerPage());
        $this->variableApi->set('MUYourAnswersModule', 'linkOwnQuestionsOnAccountPage', $this->getLinkOwnQuestionsOnAccountPage());
        $this->variableApi->set('MUYourAnswersModule', 'showOnlyOwnEntries', $this->getShowOnlyOwnEntries());
        $this->variableApi->set('MUYourAnswersModule', 'filterDataByLocale', $this->getFilterDataByLocale());
        $this->variableApi->set('MUYourAnswersModule', 'moderationGroupForAnswers', $this->getModerationGroupForAnswers());
        $this->variableApi->set('MUYourAnswersModule', 'moderationGroupForQuestions', $this->getModerationGroupForQuestions());
        $this->variableApi->set('MUYourAnswersModule', 'allowModerationSpecificCreatorForAnswer', $this->getAllowModerationSpecificCreatorForAnswer());
        $this->variableApi->set('MUYourAnswersModule', 'allowModerationSpecificCreationDateForAnswer', $this->getAllowModerationSpecificCreationDateForAnswer());
        $this->variableApi->set('MUYourAnswersModule', 'allowModerationSpecificCreatorForQuestion', $this->getAllowModerationSpecificCreatorForQuestion());
        $this->variableApi->set('MUYourAnswersModule', 'allowModerationSpecificCreationDateForQuestion', $this->getAllowModerationSpecificCreationDateForQuestion());
        $this->variableApi->set('MUYourAnswersModule', 'enabledFinderTypes', $this->getEnabledFinderTypes());
    }
}
