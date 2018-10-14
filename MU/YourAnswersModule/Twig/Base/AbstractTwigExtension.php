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

namespace MU\YourAnswersModule\Twig\Base;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extension;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use MU\YourAnswersModule\Helper\ListEntriesHelper;
use MU\YourAnswersModule\Helper\EntityDisplayHelper;
use MU\YourAnswersModule\Helper\WorkflowHelper;

/**
 * Twig extension base class.
 */
abstract class AbstractTwigExtension extends Twig_Extension
{
    use TranslatorTrait;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;
    
    /**
     * TwigExtension constructor.
     *
     * @param TranslatorInterface $translator     Translator service instance
     * @param RequestStack        $requestStack   RequestStack service instance
     * @param VariableApiInterface   $variableApi    VariableApi service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     * @param WorkflowHelper      $workflowHelper WorkflowHelper service instance
     * @param ListEntriesHelper   $listHelper     ListEntriesHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        VariableApiInterface $variableApi,
        EntityDisplayHelper $entityDisplayHelper,
        WorkflowHelper $workflowHelper,
        ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
        $this->variableApi = $variableApi;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->workflowHelper = $workflowHelper;
        $this->listHelper = $listHelper;
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
     * Returns a list of custom Twig functions.
     *
     * @return \Twig_SimpleFunction[] List of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('muyouranswersmodule_moderationObjects', [$this, 'getModerationObjects']),
            new \Twig_SimpleFunction('muyouranswersmodule_objectTypeSelector', [$this, 'getObjectTypeSelector']),
            new \Twig_SimpleFunction('muyouranswersmodule_templateSelector', [$this, 'getTemplateSelector'])
        ];
    }
    
    /**
     * Returns a list of custom Twig filters.
     *
     * @return \Twig_SimpleFilter[] List of filters
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('muyouranswersmodule_listEntry', [$this, 'getListEntry']),
            new \Twig_SimpleFilter('muyouranswersmodule_icalText', [$this, 'formatIcalText']),
            new \Twig_SimpleFilter('muyouranswersmodule_formattedTitle', [$this, 'getFormattedEntityTitle']),
            new \Twig_SimpleFilter('muyouranswersmodule_objectState', [$this, 'getObjectState'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * The muyouranswersmodule_objectState filter displays the name of a given object's workflow state.
     * Examples:
     *    {{ item.workflowState|muyouranswersmodule_objectState }}        {# with visual feedback #}
     *    {{ item.workflowState|muyouranswersmodule_objectState(false) }} {# no ui feedback #}
     *
     * @param string  $state      Name of given workflow state
     * @param boolean $uiFeedback Whether the output should include some visual feedback about the state
     *
     * @return string Enriched and translated workflow state ready for display
     */
    public function getObjectState($state = 'initial', $uiFeedback = true)
    {
        $stateInfo = $this->workflowHelper->getStateInfo($state);
    
        $result = $stateInfo['text'];
        if (true === $uiFeedback) {
            $result = '<span class="label label-' . $stateInfo['ui'] . '">' . $result . '</span>';
        }
    
        return $result;
    }
    
    
    /**
     * The muyouranswersmodule_listEntry filter displays the name
     * or names for a given list item.
     * Example:
     *     {{ entity.listField|muyouranswersmodule_listEntry('entityName', 'fieldName') }}
     *
     * @param string $value      The dropdown value to process
     * @param string $objectType The treated object type
     * @param string $fieldName  The list field's name
     * @param string $delimiter  String used as separator for multiple selections
     *
     * @return string List item name
     */
    public function getListEntry($value, $objectType = '', $fieldName = '', $delimiter = ', ')
    {
        if ((empty($value) && $value != '0') || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        return $this->listHelper->resolve($value, $objectType, $fieldName, $delimiter);
    }
    
    
    /**
     * The muyouranswersmodule_moderationObjects function determines the amount of unapproved objects.
     * It uses the same logic as the moderation block and the pending content listener.
     *
     * @return string The output of the plugin
     */
    public function getModerationObjects()
    {
        return $this->workflowHelper->collectAmountOfModerationItems();
    }
    
    
    /**
     * The muyouranswersmodule_icalText filter outputs a given text for the ics output format.
     * Example:
     *     {{ 'someString'|muyouranswersmodule_icalText }}
     *
     * @param string $string The given output string
     *
     * @return string Processed string for ics output
     */
    public function formatIcalText($string)
    {
        $result = preg_replace('/<a href="(.*)">.*<\/a>/i', "$1", $string);
        $result = str_replace('�', 'Euro', $result);
        $result = ereg_replace("(\r\n|\n|\r)", '=0D=0A', $result);
    
        return ';LANGUAGE=' . $this->requestStack->getCurrentRequest()->getLocale() . ';ENCODING=QUOTED-PRINTABLE:' . $result . "\r\n";
    }
    
    
    /**
     * The muyouranswersmodule_objectTypeSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getObjectTypeSelector()
    {
        $result = [];
    
        $result[] = [
            'text' => $this->__('Answers'),
            'value' => 'answer'
        ];
        $result[] = [
            'text' => $this->__('Questions'),
            'value' => 'question'
        ];
    
        return $result;
    }
    
    
    /**
     * The muyouranswersmodule_templateSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getTemplateSelector()
    {
        $result = [];
    
        $result[] = [
            'text' => $this->__('Only item titles'),
            'value' => 'itemlist_display.html.twig'
        ];
        $result[] = [
            'text' => $this->__('With description'),
            'value' => 'itemlist_display_description.html.twig'
        ];
        $result[] = [
            'text' => $this->__('Custom template'),
            'value' => 'custom'
        ];
    
        return $result;
    }
    
    /**
     * The muyouranswersmodule_formattedTitle filter outputs a formatted title for a given entity.
     * Example:
     *     {{ myPost|muyouranswersmodule_formattedTitle }}
     *
     * @param object $entity The given entity instance
     *
     * @return string The formatted title
     */
    public function getFormattedEntityTitle($entity)
    {
        return $this->entityDisplayHelper->getFormattedTitle($entity);
    }
}