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

namespace MU\YourAnswersModule\Form\Type\Base;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use MU\YourAnswersModule\Form\Type\Field\MultiListType;
use MU\YourAnswersModule\AppSettings;
use MU\YourAnswersModule\Helper\ListEntriesHelper;

/**
 * Configuration form type base class.
 */
abstract class AbstractConfigType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * ConfigType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     * @param ListEntriesHelper $listHelper ListEntriesHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listHelper
    ) {
        $this->setTranslator($translator);
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
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addListViewsFields($builder, $options);
        $this->addModerationFields($builder, $options);
        $this->addIntegrationFields($builder, $options);

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds fields for list views fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addListViewsFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('answerEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Answer entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of answers shown per page')
            ],
            'help' => $this->__('The amount of answers shown per page'),
            'empty_data' => 10,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the answer entries per page.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => true,
            'scale' => 0
        ]);
        
        $builder->add('linkOwnAnswersOnAccountPage', CheckboxType::class, [
            'label' => $this->__('Link own answers on account page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to add a link to answers of the current user on his account page')
            ],
            'help' => $this->__('Whether to add a link to answers of the current user on his account page'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The link own answers on account page option')
            ],
            'required' => false,
        ]);
        
        $builder->add('questionEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Question entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of questions shown per page')
            ],
            'help' => $this->__('The amount of questions shown per page'),
            'empty_data' => 10,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the question entries per page.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => true,
            'scale' => 0
        ]);
        
        $builder->add('linkOwnQuestionsOnAccountPage', CheckboxType::class, [
            'label' => $this->__('Link own questions on account page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to add a link to questions of the current user on his account page')
            ],
            'help' => $this->__('Whether to add a link to questions of the current user on his account page'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The link own questions on account page option')
            ],
            'required' => false,
        ]);
        
        $builder->add('showOnlyOwnEntries', CheckboxType::class, [
            'label' => $this->__('Show only own entries') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether only own entries should be shown on view pages by default or not')
            ],
            'help' => $this->__('Whether only own entries should be shown on view pages by default or not'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The show only own entries option')
            ],
            'required' => false,
        ]);
        
        $builder->add('filterDataByLocale', CheckboxType::class, [
            'label' => $this->__('Filter data by locale') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether automatically filter data in the frontend based on the current locale or not')
            ],
            'help' => $this->__('Whether automatically filter data in the frontend based on the current locale or not'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The filter data by locale option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for moderation fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addModerationFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('moderationGroupForAnswers', EntityType::class, [
            'label' => $this->__('Moderation group for answers') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Used to determine moderator user accounts for sending email notifications.')
            ],
            'help' => $this->__('Used to determine moderator user accounts for sending email notifications.'),
            'empty_data' => 2,
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Choose the moderation group for answers')
            ],
            'required' => true,
            // Zikula core should provide a form type for this to hide entity details
            'class' => 'ZikulaGroupsModule:GroupEntity',
            'choice_label' => 'name',
            'choice_value' => 'gid'
        ]);
        
        $builder->add('moderationGroupForQuestions', EntityType::class, [
            'label' => $this->__('Moderation group for questions') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Used to determine moderator user accounts for sending email notifications.')
            ],
            'help' => $this->__('Used to determine moderator user accounts for sending email notifications.'),
            'empty_data' => 2,
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Choose the moderation group for questions')
            ],
            'required' => true,
            // Zikula core should provide a form type for this to hide entity details
            'class' => 'ZikulaGroupsModule:GroupEntity',
            'choice_label' => 'name',
            'choice_value' => 'gid'
        ]);
        
        $builder->add('allowModerationSpecificCreatorForAnswer', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creator for answer') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a user which will be set as creator.')
            ],
            'help' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creator for answer option')
            ],
            'required' => false,
        ]);
        
        $builder->add('allowModerationSpecificCreationDateForAnswer', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creation date for answer') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a custom creation date.')
            ],
            'help' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creation date for answer option')
            ],
            'required' => false,
        ]);
        
        $builder->add('allowModerationSpecificCreatorForQuestion', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creator for question') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a user which will be set as creator.')
            ],
            'help' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creator for question option')
            ],
            'required' => false,
        ]);
        
        $builder->add('allowModerationSpecificCreationDateForQuestion', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creation date for question') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a custom creation date.')
            ],
            'help' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creation date for question option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for integration fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIntegrationFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'enabledFinderTypes');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('enabledFinderTypes', MultiListType::class, [
            'label' => $this->__('Enabled finder types') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Which sections are supported in the Finder component (used by Scribite plug-ins).')
            ],
            'help' => $this->__('Which sections are supported in the Finder component (used by Scribite plug-ins).'),
            'empty_data' => 'answer###question',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the enabled finder types.')
            ],
            'required' => false,
            'placeholder' => $this->__('Choose an option'),
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => true,
            'expanded' => false
        ]);
    }

    /**
     * Adds submit buttons.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('save', SubmitType::class, [
            'label' => $this->__('Update configuration'),
            'icon' => 'fa-check',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);
        $builder->add('reset', ResetType::class, [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'muyouranswersmodule_config';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data
                'data_class' => AppSettings::class,
            ]);
    }
}
