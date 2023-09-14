<?php
/**
 * Category type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Record;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class RecordType.
 */
class RecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'label' => 'label.category',
                    'required' => true,
                ],
            )
            ->add("title",
                TextType::class,
                [
                    'label' => 'label.title',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ]
            )
            ->add("text",
                TextType::class,
                [
                    'label' => 'label.text',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ]
            );




    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Record::class]);
    }
}
