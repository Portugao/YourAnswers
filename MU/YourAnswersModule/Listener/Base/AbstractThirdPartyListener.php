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

namespace MU\YourAnswersModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zikula\Common\Collection\Collectible\PendingContentCollectible;
use Zikula\Common\Collection\Container;
use Zikula\Core\Event\GenericEvent;
use MU\YourAnswersModule\Helper\WorkflowHelper;
use Zikula\ScribiteModule\Event\EditorHelperEvent;

/**
 * Event handler implementation class for special purposes and 3rd party api support.
 */
abstract class AbstractThirdPartyListener implements EventSubscriberInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * ThirdPartyListener constructor.
     *
     * @param Filesystem   $filesystem   Filesystem service instance
     * @param RequestStack $requestStack RequestStack service instance
     * @param WorkflowHelper $workflowHelper WorkflowHelper service instance
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem, RequestStack $requestStack, WorkflowHelper $workflowHelper)
    {
        $this->filesystem = $filesystem;
        $this->requestStack = $requestStack;
        $this->workflowHelper = $workflowHelper;
    }
    
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            'get.pending_content'                     => ['pendingContentListener', 5],
            'module.scribite.editorhelpers'           => ['getEditorHelpers', 5],
            'moduleplugin.ckeditor.externalplugins'   => ['getCKEditorPlugins', 5],
            'moduleplugin.quill.externalplugins'      => ['getQuillPlugins', 5],
            'moduleplugin.summernote.externalplugins' => ['getSummernotePlugins', 5],
            'moduleplugin.tinymce.externalplugins'    => ['getTinyMcePlugins', 5]
        ];
    }
    
    /**
     * Listener for the `get.pending_content` event which collects information from modules
     * about pending content items waiting for approval.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * @param GenericEvent $event The event instance
     */
    public function pendingContentListener(GenericEvent $event)
    {
        $collection = new Container('MUYourAnswersModule');
        $amounts = $this->workflowHelper->collectAmountOfModerationItems();
        if (count($amounts) > 0) {
            foreach ($amounts as $amountInfo) {
                $aggregateType = $amountInfo['aggregateType'];
                $description = $amountInfo['description'];
                $amount = $amountInfo['amount'];
                $route = 'muyouranswersmodule_' . strtolower($amountInfo['objectType']) . '_adminview';
                $routeArgs = [
                    'workflowState' => $amountInfo['state']
                ];
                $item = new PendingContentCollectible($aggregateType, $description, $amount, $route, $routeArgs);
                $collection->add($item);
            }
        
            // add collected items for pending content
            if ($collection->count() > 0) {
                $event->getSubject()->add($collection);
            }
        }
    }
    
    /**
     * Listener for the `module.scribite.editorhelpers` event.
     *
     * This occurs when Scribite adds pagevars to the editor page.
     * MUYourAnswersModule will use this to add a javascript helper to add custom items.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * @param EditorHelperEvent $event The event instance
     */
    public function getEditorHelpers(EditorHelperEvent $event)
    {
        // install assets for Scribite plugins
        $targetDir = 'web/modules/muyouranswers';
        if (!$this->filesystem->exists($targetDir)) {
            $moduleDirectory = str_replace('Listener/Base', '', __DIR__);
            if (is_dir($originDir = $moduleDirectory . 'Resources/public')) {
                $this->filesystem->symlink($originDir, $targetDir, true);
            }
        }
    
        $event->getHelperCollection()->add(
            [
                'module' => 'MUYourAnswersModule',
                'type' => 'javascript',
                'path' => $this->requestStack->getCurrentRequest()->getBasePath() . '/web/modules/muyouranswers/js/MUYourAnswersModule.Finder.js'
            ]
        );
    }
    
    /**
     * Listener for the `moduleplugin.ckeditor.externalplugins` event.
     *
     * Adds external plugin to CKEditor.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * @param GenericEvent $event The event instance
     */
    public function getCKEditorPlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'muyouranswersmodule',
            'path' => $this->requestStack->getCurrentRequest()->getBasePath() . '/web/modules/muyouranswers/scribite/CKEditor/muyouranswersmodule/',
            'file' => 'plugin.js',
            'img' => 'ed_muyouranswersmodule.gif'
        ]);
    }
    
    /**
     * Listener for the `moduleplugin.quill.externalplugins` event.
     *
     * Adds external plugin to Quill.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * @param GenericEvent $event The event instance
     */
    public function getQuillPlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'muyouranswersmodule',
            'path' => $this->requestStack->getCurrentRequest()->getBasePath() . '/web/modules/muyouranswers/scribite/Quill/muyouranswersmodule/plugin.js'
        ]);
    }
    
    /**
     * Listener for the `moduleplugin.summernote.externalplugins` event.
     *
     * Adds external plugin to Summernote.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * @param GenericEvent $event The event instance
     */
    public function getSummernotePlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'muyouranswersmodule',
            'path' => $this->requestStack->getCurrentRequest()->getBasePath() . '/web/modules/muyouranswers/scribite/Summernote/muyouranswersmodule/plugin.js'
        ]);
    }
    
    /**
     * Listener for the `moduleplugin.tinymce.externalplugins` event.
     *
     * Adds external plugin to TinyMce.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * @param GenericEvent $event The event instance
     */
    public function getTinyMcePlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'muyouranswersmodule',
            'path' => $this->requestStack->getCurrentRequest()->getBasePath() . '/web/modules/muyouranswers/scribite/TinyMce/muyouranswersmodule/plugin.js'
        ]);
    }
}