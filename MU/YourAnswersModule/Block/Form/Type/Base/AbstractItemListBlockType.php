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

namespace MU\YourAnswersModule\Block\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * List block form type base class.
 */
abstract class AbstractItemListBlockType extends AbstractType
{
    use TranslatorTrait;

    /**
     * ItemListBlockType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
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
        $this->addObjectTypeField($builder, $options);
        $this->addSortingField($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addTemplateFields($builder, $options);
        $this->addFilterField($builder, $options);
    }

    /**
     * Adds an object type field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('objectType', ChoiceType::class, [
            'label' => $this->__('Object type', 'muyouranswersmodule') . ':',
            'empty_data' => 'question',
            'attr' => [
                'title' => $this->__('If you change this please save the block once to reload the parameters below.', 'muyouranswersmodule')
            ],
            'help' => $this->__('If you change this please save the block once to reload the parameters below.', 'muyouranswersmodule'),
            'choices' => [
                $this->__('Answers', 'muyouranswersmodule') => 'answer',
                $this->__('Questions', 'muyouranswersmodule') => 'question'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a sorting field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSortingField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('sorting', ChoiceType::class, [
            'label' => $this->__('Sorting', 'muyouranswersmodule') . ':',
            'empty_data' => 'default',
            'choices' => [
                $this->__('Random', 'muyouranswersmodule') => 'random',
                $this->__('Newest', 'muyouranswersmodule') => 'newest',
                $this->__('Updated', 'muyouranswersmodule') => 'updated',
                $this->__('Default', 'muyouranswersmodule') => 'default'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAmountField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('amount', IntegerType::class, [
            'label' => $this->__('Amount', 'muyouranswersmodule') . ':',
            'attr' => [
                'maxlength' => 2,
                'title' => $this->__('The maximum amount of items to be shown.', 'muyouranswersmodule') . ' ' . $this->__('Only digits are allowed.', 'muyouranswersmodule')
            ],
            'help' => $this->__('The maximum amount of items to be shown.', 'muyouranswersmodule') . ' ' . $this->__('Only digits are allowed.', 'muyouranswersmodule'),
            'empty_data' => 5,
            'scale' => 0
        ]);
    }

    /**
     * Adds template fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addTemplateFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('template', ChoiceType::class, [
                'label' => $this->__('Template', 'muyouranswersmodule') . ':',
                'empty_data' => 'itemlist_display.html.twig',
                'choices' => [
                    $this->__('Only item titles', 'muyouranswersmodule') => 'itemlist_display.html.twig',
                    $this->__('With description', 'muyouranswersmodule') => 'itemlist_display_description.html.twig',
                    $this->__('Custom template', 'muyouranswersmodule') => 'custom'
                ],
                'multiple' => false,
                'expanded' => false
            ])
            ->add('customTemplate', TextType::class, [
                'label' => $this->__('Custom template', 'muyouranswersmodule') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 80,
                    'title' => $this->__('Example', 'muyouranswersmodule') . ': itemlist_[objectType]_display.html.twig'
                ],
                'help' => $this->__('Example', 'muyouranswersmodule') . ': <em>itemlist_[objectType]_display.html.twig</em>'
            ])
        ;
    }

    /**
     * Adds a filter field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addFilterField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('filter', TextType::class, [
            'label' => $this->__('Filter (expert option)', 'muyouranswersmodule') . ':',
            'required' => false,
            'attr' => [
                'maxlength' => 255,
                'title' => $this->__('Example', 'muyouranswersmodule') . ': tbl.age >= 18'
            ],
            'help' => $this->__('Example', 'muyouranswersmodule') . ': tbl.age >= 18'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'muyouranswersmodule_listblock';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'object_type' => 'question'
            ])
            ->setRequired(['object_type'])
            ->setAllowedTypes('object_type', 'string')
        ;
    }
}