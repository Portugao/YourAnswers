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

namespace MU\YourAnswersModule\Entity;

use MU\YourAnswersModule\Entity\Base\AbstractAnswerEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for answer entities.
 * @ORM\Entity(repositoryClass="MU\YourAnswersModule\Entity\Repository\AnswerRepository")
 * @ORM\Table(name="mu_youranswers_answer",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class AnswerEntity extends BaseEntity
{
    // feel free to add your own methods here
}
