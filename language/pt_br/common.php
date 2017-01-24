<?php
/**
*
* @package phpBB Extension - phpBB Wall
* @author Saulo Henrique
* @version 1.0.0
* @copyright (c) 2017 http://www.suportephpbb.com.br
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/


if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(

	'WALL_PAGE'			=> 'Mural',
	'WALL_MAIN'			=> 'Página Principal',	
	'WALL_NOSET'		=> 'Não informado',	
	

	'WALL_ABOUT_BLOCK'              => 'Sobre mim',	
	'WALL_FRIENDS_BLOCK'              => 'Pedido de amizade',	
	'WALL_NEW_USERS_BLOCK'            => 'Novos membros',	
	'WALL_LATEST_TOPICS_BLOCK'        => 'Tópicos recentes do fórum',	
	
		// WALL status
	
	'WALL_STATUS_PH_STATUS'		    	=> 'Compartilhar algo...',	
	'WALL_STATUS_PH_IMG'	         	=> 'Acrescente uma descrição...',	
	'WALL_STATUS_POST'	             	=> 'Postar',	
	'WALL_STATUS_POST_IMG'	         	=> 'Compartilhe uma imagem',		
	'WALL_STATUS_POST_STA'	         	=> 'Status do post',	
	'WALL_STATUS_DELETE_INFO'        	=> 'Tem a certeza de que pretende eliminar este status?',
	'WALL_COMMENTS_PH'	             	=> 'Adicione um comentário...',			
	'WALL_COMMENTS_POST'             	=> 'Comentar',			
	'WALL_COMMENTS_COUNT'             	=> '%1$s Comentários',
	'WALL_COMMENTS_HIDE'             	=> 'Esconder comentários',
	'WALL_COMMENTS_EDITING'	         	=> 'Editar comentário...',	
	'WALL_COMMENTS_DELETE_INFO'      	=> 'Tem certeza de que deseja excluir este comentário?',
	
	
	'WALL_STATUS_SHARE_INFO'	     => '%1$s compartilhado um(a) %2$s no mural de %3$s.',
	'WALL_STATUS_TYPE'			     => array(
		'1'	     => 'status',
		'2'	     => 'comment',
		'3'	     => 'photo',
		'4'  	 => 'song',
		),	
		
	'WALL_EDIT'	                     	=> 'Editar',		
	'WALL_UPDATE'                    	=> 'Atualizar',		
	'WALL_DELETE'                    	=> 'Apagar',
	'WALL_CANCEL'	                 	=> 'Cancelar',		
	'WALL_MORE'                      	=> 'Ler mais',	
	
	// WALL ACP
	
	'ACP_WALL_TITLE'				 	=> 'phpBB Wall',
	'ACP_WALL_SETTINGS_EXPLAIN' 	 	=> 'Esta é a página de configuração “phpBB Wall extension”.',
	'ACP_WALL_VERSION'          	 	=> 'Versão da extensão',
	'ACP_WALL_GENERAL'		         	=> 'Configurações Gerais',
	'ACP_WALL_ENABLED'				 	=> 'Atuivar phpBB Wall',
	'ACP_WALL_PROFILE_SETTINGS'    	 	=> 'Configurações de perfil',
	'ACP_WALL_PROFILE'		         	=> 'Usar perfil do phpBB Wall',
	'ACP_WALL_PROFILE_EXPLAIN'  	 	=> 'Substite o sistema de perfil padrão do phpBB.',
	'ACP_WALL_MUSIC_ENABLED'		    => 'Permitir que os usuários façam upload e compartilhem música',
	'ACP_WALL_MUSIC_ENABLED_EXPLAIN'    => 'PHP post_max_size deve ser definido maior do que 8M, 20M vai funcionar muito bem para o upload de música.',

	'ACP_WALL_STATUS'	             	=> 'Configurações de status',	 
	'ACP_WALL_STATUS_SMILIES' 	        => 'Permitir smilies no status',  
	'ACP_WALL_STATUS_BBCODE' 	        => 'Permitir BBcodes no status',  
	'ACP_WALL_STATUS_URL' 	            => 'Permitir URLs no status', 	
	
	'ACP_WALL_COMMENTS'	             	=> 'Configurações dos comentários',
	'ACP_WALL_CMT_EDIT'              	=> 'Editar Comentários', 
	'ACP_WALL_CMT_EDIT_EXPLAIN'      	=> 'Permitir que os usuários editem seus comentários.', 
	'ACP_WALL_CMT_EDIT_TIME'         	=> 'Tempo para editar os comentários',  
	'ACP_WALL_CMT_EDIT_TIME_EXPLAIN' 	=> 'Tempo máximo para editar os comentários (segundos).',  
	'ACP_WALL_CMT_SMILIES' 	            => 'Permitir smilies nos comentários de status',  
	'ACP_WALL_CMT_BBCODE' 	            => 'Permitir BBcodes nos comentários de status',  
	'ACP_WALL_CMT_URL' 	                => 'Permitir URLs nos comentários de status', 
	
	'ACP_WALL_BLOCKS'	             	=> 'Configurações dos blocos', 
	'ACP_WALL_BLOCKS_NEW_USERS'      	=> 'Ativar bloco de novos usuários',  
	'ACP_WALL_BLOCKS_NEW_USERS_MAX'  	=> 'Máximo de usuários a serem exibidos no bloco de novos usuários',  
	'ACP_WALL_BLOCKS_FRIENDS_REQ'    	=> 'Ativar o bloco de solicitação de amizades',  
	'ACP_WALL_BLOCKS_LATEST_TOPICS'  	=> 'Ativar o bloco de discussões recentes',  
	'ACP_WALL_BLOCKS_LATEST_TOPICS_MAX' => 'Número máximo de tópicos a serem exibidos no bloco de discussões recentes',  
	
	'ACP_WALL_SETTING_SAVED'		 	=> 'As configurações foram salvas com sucesso!',


	// WALL tabs
	'WALL_TAB_MAIN'		                => 'Atividade',	
	'WALL_TAB_INFO'		              	=> 'Informação',	
	'WALL_TAB_FRIENDS'		          	=> 'Amigos',	
	'WALL_TAB_PHOTOS'		          	=> 'Fotos',	
	'WALL_TAB_MUSIC'		          	=> 'Música',	

	// WALL Likes
	'WALL_LIKE'		               	  => 'Curtir',
	'WALL_UNLIKE'		              => 'Descurtir',	
	'WALL_LIKE_U'	                  => 'Você',	
	'WALL_LIKE_AND'		              => ' e ',
	'WALL_LIKE_AND_OTHERS'	          => ' e <strong>%1$s outros</strong> ',	
	'WALL_LIKE_THIS'	              => 'curtiu isso',		
	'WALL_LIKE_THIS_S'	              => 'curtiram isso',		
	'WALL_LIKE_LIST'	              => 'Lista de curtidas',			
	
	
	// WALL notifications
    'NOTIFICATION_TYPE_WALL_COMMENTS' => 'Comentários no Mural',	 
    'NOTIFICATION_TYPE_WALL_STATUS'	  => 'Status no Mural',	 
    'NOTIFICATION_TYPE_WALL_LIKES'    => 'Curtidas no Mural',		
	'NOTIFICATION_WALL_ADD_STATUS'	  => '%1$s Adicionou algo ao seu mural "%2$s"',		
	'NOTIFICATION_WALL_ADD_CMT'	      => '%1$s comentou seu status "%2$s"',
	'NOTIFICATION_WALL_LIKE_STATUS'	  => '%1$s curtiu o seu status "%2$s"',
	
	
	
	// WALL Friends system			
	'WALL_FRIENDS'	                  => 'Amigos',	
	'WALL_FRIENDS_ADD'		          => 'Adicionar como amigo',
	'WALL_FRIENDS_CANCEL_REQ'	      => 'Cancelar pedido de amizade',
	'WALL_FRIENDS_CANCEL_REQ_SHORT'	  => 'Remover',	
	'WALL_FRIENDS_ACCEPT_REQ_SHORT'   => 'Aceitar',		
	'WALL_FRIENDS_ACCEPT_REQ'	      => 'Aceitar solicitação de amizade',	
	'WALL_FRIENDS_REMOVE_BLOCK'	      => 'Desbloquear',	 
	 
	
	/**
	 * WALL TIME AGO
	 */
	'WALL_TIME_AGO'					  => '%1$u %2$s atrás',
	'WALL_TIME_FROM_NOW'			  => '%1$u %2$s a partir de agora',
	'WALL_TIME_PERIODS'				  => array(
		'SECOND'	 => 'segundo',
		'SECONDS'	 => 'segundos',
		'MINUTE'	 => 'minuto',
		'MINUTES'	 => 'minutos',
		'HOUR'		 => 'hora',
		'HOURS'		 => 'horas',
		'DAY'		 => 'dia',
		'DAYS'		 => 'dias',
		'WEEK'		 => 'semana',
		'WEEKS'		 => 'semanas',
		'MONTH'		 => 'mês',
		'MONTHS'	 => 'meses',
		'YEAR'		 => 'ano',
		'YEARS'		 => 'anos',
		'DECADE'	 => 'década',
		'DECADES'	 => 'décadas',
		),
		
	'WALL_SEARCH_NR'			      => 'Nenhum resultado',			
	'UCP_PROFILE_AVATAR'	          => 'Editar o meu avatar',	
	'WALL_PROFILE_UPDATE_COVER'		  => 'Alterar capa',
	'WALL_PROFILE_PICK_COVER'         => 'Selecione uma foto para usá-la como capa',	
	'WALL_PROFILE_COVER_DRAG'		  => 'Arraste para reposicionar ...',
	'WALL_PROFILE_NO_COVER'           => 'Desculpe, mas você não tem nenhuma foto para usá-lo como capa. Carregue uma primeiro.',
	
	
	/**
	 * WALL Music
	 */	
	'WALL_MUSIC_TITLE'				  => 'Música',	
	'WALL_MUSIC_UPLOAD'		          => 'Enviar música',
	'WALL_MUSIC_LIMIT'			      => 'Desculpe, mas você não pode carregar mais de 15 músicas!',	
	'WALL_MUSIC_DOWNLOAD'		      => 'Download',	
    'WALL_MUSIC_ALLOWED'			  => 'Arquivos permitidos',
    'WALL_MUSIC_ADDED'			      => 'adicionou uma nova música ',
	'WALL_MUSIC_DELETE'			      => 'Excluir',	
	'WALL_MUSIC_RETURN'			      => 'Voltar',
	'WALL_MUSIC_PLAY'				  => 'Play',						
	'WALL_MUSIC_JUST'				  => 'Agora mesmo',	
	
	
// Music ACP

    'WALL_MUSIC_RECENT'			      => 'Música recente',
    'WALL_MUSIC_SONG'				  => '1 música',
	'WALL_MUSIC_SONGS'				  => 'músicas',		
	'WALL_MUSIC_LMUSIC'			      => 'Última lista de músicas na página de atividade',
	'WALL_MUSIC_STATS'				  => 'Total de músicas do usuário no perfil estatísticas',
	
));


