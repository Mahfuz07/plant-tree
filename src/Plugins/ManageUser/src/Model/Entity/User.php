<?php
namespace ManageUser\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * Role Entity
 *
 * @property int $id
 * @property string $title
 * @property string|null $alias
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $updated
 * @property bool $is_sync
 *
 * @property \ManageUser\Model\Entity\User[] $users
 */
class User extends Entity
{

   /* protected function _setPassword($value)
    {
        if (strlen($value)) {
            $hasher = new DefaultPasswordHasher();

            return $hasher->hash($value);
        }
    }*/
}
