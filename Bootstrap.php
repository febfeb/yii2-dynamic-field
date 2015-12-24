<?php
/**
 * @link http://indoarta.co.id/
 * @copyright Copyright (c) 2015 Indoartha Citra Media, Indonesia
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace febfeb\dynamicfield;

use app\components\NodeLogger;
use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package febfeb\dynamicfield
 * @author Febrianto Arif <febfeb.90@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->setModule("dynamicfield", 'febfeb\dynamicfield\modules\Module');
    }
}