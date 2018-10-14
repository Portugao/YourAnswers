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

namespace MU\YourAnswersModule\Block\Base;

use Zikula\BlocksModule\AbstractBlockHandler;

/**
 * Moderation block base class.
 */
abstract class AbstractModerationBlock extends AbstractBlockHandler
{
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->__('Your answers moderation', 'muyouranswersmodule');
    }
    
    /**
     * @inheritDoc
     */
    public function display(array $properties = [])
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('MUYourAnswersModule:ModerationBlock:', "$properties[title]::", ACCESS_OVERVIEW)) {
            return '';
        }
    
        $currentUserApi = $this->get('zikula_users_module.current_user');
        if (!$currentUserApi->isLoggedIn()) {
            return '';
        }
    
        $template = $this->getDisplayTemplate();
    
        $workflowHelper = $this->get('mu_youranswers_module.workflow_helper');
        $amounts = $workflowHelper->collectAmountOfModerationItems();
    
        // set a block title
        if (empty($properties['title'])) {
            $properties['title'] = $this->__('Moderation', 'muyouranswersmodule');
        }
    
        return $this->renderView($template, ['moderationObjects' => $amounts]);
    }
    
    /**
     * Returns the template used for output.
     *
     * @return string the template path
     */
    protected function getDisplayTemplate()
    {
        return '@MUYourAnswersModule/Block/moderation.html.twig';
    }
}
