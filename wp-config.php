<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações
// com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'layout');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** Nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ')|_5cF5QWK3,v(g.dF8}8[F1@(`Q+QVP$4#)#l(01|y%nDQ[y4lGSg0:kHg7w?:a');
define('SECURE_AUTH_KEY',  't0;NI42N[lOA-|V}-87,|!jOid@`l<,wE|e,zq4QtA0iS%rffn;/JB7%i<r(vLkG');
define('LOGGED_IN_KEY',    'l6:CZJ4#[Fi5H-p4`a@77HrEO,v:H^.)4%/;x2O*^E{}_!EV] C@^-(PtC22Hu<n');
define('NONCE_KEY',        '=PKZrhA:.zhVyV1jtX}`jh{y`RUq*U!%X)TjP#_NczKKa4~$cFhQ_msV2F8Xb31j');
define('AUTH_SALT',        'SP2d$#)2~6V5OwcjJd&<J_~FY#xp|eo;Pv=>v?*]*mcI1SeuK4o>0jWrvlM;v3E~');
define('SECURE_AUTH_SALT', '@d1pcGN{IzL~d%YlG?eDHRW/tO;O#1,bUF{1K+TANYQpCBw-JjVG1gj @M0:&sot');
define('LOGGED_IN_SALT',   ',)*M` #jZ}T5%b_|5!hY<BQ;:9L,{$.FA&K3IUw# 6RpIYavwA#:7i_}3a-(RC[%');
define('NONCE_SALT',       '4Z:u,UqfyOEi6jw0M!l.)=IJJw#SA?r2MTeTSk)kS6nU<N/bO>z1]^S/J]hZU`{f');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
