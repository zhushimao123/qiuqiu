<?php
/**
 * WordPress基础配置文件。
 *
 * 这个文件被安装程序用于自动生成wp-config.php配置文件，
 * 您可以不使用网站，您需要手动复制这个文件，
 * 并重命名为“wp-config.php”，然后填入相关信息。
 *
 * 本文件包含以下配置选项：
 *
 * * MySQL设置
 * * 密钥
 * * 数据库表名前缀
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/zh-cn:%E7%BC%96%E8%BE%91_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL 设置 - 具体信息来自您正在使用的主机 ** //
/** WordPress数据库的名称 */
define('DB_NAME', 'weixin_shop');

/** MySQL数据库用户名 */
define('DB_USER', 'root');

/** MySQL数据库密码 */
define('DB_PASSWORD', 'zsm123..0');

/** MySQL主机 */
define('DB_HOST', '127.0.0.1');

/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8mb4');

/** 数据库整理类型。如不确定请勿更改 */
define('DB_COLLATE', '');

/**#@+
 * 身份认证密钥与盐。
 *
 * 修改为任意独一无二的字串！
 * 或者直接访问{@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org密钥生成服务}
 * 任何修改都会导致所有cookies失效，所有用户将必须重新登录。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '^B3eE|R,SRIA!Yp7[{jJ2WxMSMbUJ4)=X-K#tv-&TiMab^Ie`WO/IlLHboD-=06{');
define('SECURE_AUTH_KEY',  'C/q)s+g]pPQtDcC27%Ksc%];QA<]fGUN]NuD[9K<j4AjTS^54{tA.7U%,ee/Sg[p');
define('LOGGED_IN_KEY',    '-%p8A]^.YDSHnZGg7JnT+#omv^@;<#i>c*=:DP_,XM]&]yua#[bB.NmZyF:U*o_:');
define('NONCE_KEY',        '@U~o&gWEu]t&O[-aO8|ber$0AH~C0vDw6GCk7ZqU29;j@YU73Nzg2THG`w/gp4!=');
define('AUTH_SALT',        '}e;pt7c[<PCa55f4lNXWH;Bmm-#Y+IB+zgm~p`!ZT:6]TLi|*fx2.u`Kv~}N;cvY');
define('SECURE_AUTH_SALT', '|P:c2Z0Z#AFExs{2j2@2~a^*rB MhSkl7js:<ZhS&!+b@ya N+iNt0D&G37wq)n#');
define('LOGGED_IN_SALT',   'G6TC(lr@qk}IsGs*oGvpII%1&3pDAzu4mht_d9fUd07 t{O`H!4)n 6vKWW,l+K[');
define('NONCE_SALT',       ',q+.mS|L1#[do*t&32H#_c;vKosI(&+eJucPW>Dce4ifF[^sV27?8Tx`tW*J;73X');
/**#@-*/

/**
 * WordPress数据表前缀。
 *
 * 如果您有在同一数据库内安装多个WordPress的需求，请为每个WordPress设置
 * 不同的数据表前缀。前缀名只能为数字、字母加下划线。
 */
$table_prefix  = 'wp_';

/**
 * 开发者专用：WordPress调试模式。
 *
 * 将这个值改为true，WordPress将显示所有用于开发的提示。
 * 强烈建议插件开发者在开发环境中启用WP_DEBUG。
 *
 * 要获取其他能用于调试的信息，请访问Codex。
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* 好了！请不要再继续编辑。请保存本文件。使用愉快！ */

/** WordPress目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** 设置WordPress变量和包含文件。 */
require_once(ABSPATH . 'wp-settings.php');

