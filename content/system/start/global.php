<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
use Royalcms\Component\Support\Facades\Royalcms;

// 请求数据自动转义
$_POST      = rc_addslashes($_POST);
$_GET       = rc_addslashes($_GET);
$_REQUEST   = rc_addslashes($_REQUEST);
$_COOKIE    = rc_addslashes($_COOKIE);

// Load the default text localization domain.
RC_Locale::loadDefaultTextdomain();

//加载项目函数库
RC_Loader::load_sys_func('functions');

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

RC_Log::useDailyFiles(SITE_CACHE_PATH . 'logs/royalcms.log', 30);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

Royalcms::error(function(Exception $exception)
{
    $err = array(
        'file'      => $exception->getFile(),
        'line'      => $exception->getLine(),
        'code'      => $exception->getCode(),
        'url'       => RC_Request::fullUrl(),
    );

    RC_Logger::getLogger(RC_Logger::LOG_ERROR)->error($exception->getMessage(), $err);
});
Royalcms::error(function(Symfony\Component\HttpKernel\Exception\HttpException $exception)
{
    return RC_Response::make($exception->getMessage(), 400);
});
Royalcms::error(function(ErrorException $exception)
{
    $err = array(
        'file'      => $exception->getFile(),
        'line'      => $exception->getLine(),
        'code'      => $exception->getCode(),
        'url'       => RC_Request::fullUrl(),
    );

    RC_Logger::getLogger(RC_Logger::LOG_ERROR)->error($exception->getMessage(), $err);
    royalcms('sentry')->captureException($exception);
});
Royalcms::fatal(function(\Symfony\Component\Debug\Exception\FatalErrorException $exception)
{
    $err = array(
        'file'      => $exception->getFile(),
        'line'      => $exception->getLine(),
        'code'      => $exception->getCode(),
        'url'       => RC_Request::fullUrl(),
    );

    RC_Logger::getLogger(RC_Logger::LOG_ERROR)->error($exception->getMessage(), $err);
    royalcms('sentry')->captureException($exception);
});
Royalcms::error(function(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception)
{
    $err = array(
        'file'      => $exception->getFile(),
        'line'      => $exception->getLine(),
        'code'      => $exception->getCode(),
        'url'       => RC_Request::fullUrl(),
    );
    RC_Logger::getLogger(RC_Logger::LOG_ERROR)->error($exception->getMessage(), $err);
    royalcms('sentry')->captureException($exception);
    //404 tips
    _404($exception->getMessage());
});
Royalcms::error(function(\Symfony\Component\HttpKernel\Exception\HttpException $exception)
{
    $err = array(
        'file'      => $exception->getFile(),
        'line'      => $exception->getLine(),
        'code'      => $exception->getCode(),
        'url'       => RC_Request::fullUrl(),
    );

    RC_Logger::getLogger(RC_Logger::LOG_ERROR)->error($exception->getMessage(), $err);
    royalcms('sentry')->captureException($exception);
    //404 tips
    _404($exception->getMessage());
});


/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

Royalcms::down(function()
{
    return RC_Response::make("Be right back!", 503);
});

Royalcms::missing(function($exception)
{
    return RC_Response::make('404 Not Found', 404);
});

if (config('system.admin_enable') === true) {
    $systemBundle = function() {
        return new \Royalcms\Component\App\Bundles\SystemBundle();
    };
    RC_App::extend('admincp', $systemBundle);
    RC_App::extend('system', $systemBundle);
}

RC_Event::listen('royalcms.query', function($query, $bindings, $time) {
    if (config('system.debug')) {
        $query = str_replace('?', '"'.'%s'.'"', $query);
        $sql = vsprintf($query, $bindings);
        RC_Logger::getLogger(RC_Logger::LOG_SQL)->info('sql:'.$sql);
    }
});

RC_Event::listen('royalcms.warning.exception', function($exception) {
    if (config('system.debug')) {
        $err = array(
		            'file'      => $exception->getFile(),
		            'line'      => $exception->getLine(),
		            'code'      => $exception->getPrevious(),
		            'url'       => RC_Request::fullUrl(),
	            );
        RC_Logger::getLogger(RC_Logger::LOG_WARNING)->info($exception->getMessage(), $err);
    }
});

RC_Hook::add_action('mail_init', function () {
    RC_Mail::macro('send_mail', function ($name, $email, $subject, $content, $type = 0, $notification = false) {
        return with(new Ecjia\System\Frameworks\Component\Mailer($this))->send_mail($name, $email, $subject, $content, $type, $notification);
    });
});

// end