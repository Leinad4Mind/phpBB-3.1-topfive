<?php
/**
*
* @package Top Five
* @copyright (c) 2014 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace rmcgirr83\topfive\acp;

class topfive_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang('acp/common');
		$user->add_lang_ext('rmcgirr83/topfive', 'acp/info_acp_topfive');
		$this->tpl_name = 'acp_topfive';
		$this->page_title = $user->lang['TOPFIVE_MOD'];
		add_form_key('acp_topfive');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('acp_topfive'))
			{
				trigger_error('FORM_INVALID');
			}
			if (!function_exists('validate_data'))
			{
				include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			}

			$check_row = array('top_five_how_many' => $request->variable('top_five_how_many', 0));
			$validate_row = array('top_five_how_many' => array('num', false, 5, 100));
			$error = validate_data($check_row, $validate_row);

			// Replace "error" strings with their real, localised form
			$error = array_map(array($user, 'lang'), $error);

			if (!sizeof($error))
			{
				$config->set('top_five_how_many', $request->variable('top_five_how_many', 0));
				$config->set('top_five_ignore_inactive_users', $request->variable('top_five_ignore_inactive_users', true));
				$config->set('top_five_ignore_founder', $request->variable('top_five_ignore_founder', true));
				$config->set('top_five_show_admins_mods', $request->variable('top_five_show_admins_mods', true));
				$config->set('top_five_location', $request->variable('top_five_location', true));
				$config->set('top_five_active', $request->variable('top_five_active', true));
				// variable should be '' as it is a string ("1, 2, 3928") here, not an integer.
				$config->set('top_five_excluded', $request->variable('top_five_excluded', '0'));

				$cache->destroy('_top_five_newest_users');
				$cache->destroy('_top_five_posters');

				trigger_error($user->lang['TF_SAVED'] . adm_back_link($this->u_action));
			}
		}

		$template->assign_vars(array(
			'TF_ERROR'			=> isset($error) ? ((sizeof($error)) ? implode('<br />', $error) : '') : '',
			'HOWMANY'			=> isset($config['top_five_how_many']) ? $config['top_five_how_many'] : 0,
			'IGNORE_INACTIVE'	=> isset($config['top_five_ignore_inactive_users']) ? $config['top_five_ignore_inactive_users'] : false,
			'IGNORE_FOUNDER'	=> isset($config['top_five_ignore_founder']) ? $config['top_five_ignore_founder'] : false,
			'SHOW_ADMINS_MODS'	=> isset($config['top_five_show_admins_mods']) ? $config['top_five_show_admins_mods'] : false,
			'LOCATION'			=> isset($config['top_five_location']) ? $config['top_five_location'] : false,
			'ACTIVE'			=> isset($config['top_five_active']) ? $config['top_five_active'] : false,
			'TF_VERSION'		=> $config['top_five_version'],
			'TF_EXCLUDED'		=> isset($config['top_five_excluded']) ? $config['top_five_excluded'] : '',

			'U_ACTION'			=> $this->u_action,
		));
	}
}
