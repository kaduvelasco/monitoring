<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * lang strings
 *
 * @package    report
 * @subpackage monitoring
 * @version    1.0.1
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Monitoramento Moodle';

/**
 * Abas disponíveis na interface
 */
$string['settings'] = 'Configurações';
$string['participation'] = 'Participação';
$string['grade'] = 'Nota';
$string['dedication'] = 'Dedicação';
$string['help'] = 'Ajuda';

/**
 * Nome base para os arquivos dos relatórios gerados
 */
$string['participation_file'] = 'participacao_';
$string['grade_file'] = 'nota_';
$string['dedication_file'] = 'dedicacao_';

/**
 * Nome dos campos dos formulários
 */
$string['user_fields'] = 'Campos da tabela de usuários';
$string['user_extrafields'] = 'Campos de perfil de usuários';
$string['ignored_modules'] = 'Módulos ignorados no relatório';
$string['user_filter'] = 'Filtro para usuários';
$string['select_course'] = 'Selecione o curso';
$string['report_options'] = 'Opções do relatório';
$string['user_role'] = 'Perfil de usuário';
$string['feedback_passed'] = 'Feedback para usuários aprovados';
$string['feedback_failed'] = 'Feedback para usuários reprovados';
$string['minimum_grade'] = 'Nota mínima para aprovação';

/**
 * Textos de ajuda
 */
$string['user_fields_help'] = 'Selecione os campos do usuário que serão mostrados nos relatórios. Utilize a tecla Ctrl para selecionar mais de um ou para remover a seleção.';
$string['user_extrafields_help'] = 'Selecione os campos de perfil de usuário que serão mostrados nos relatórios. Utilize a tecla Ctrl para selecionar mais de um ou para remover a seleção.';
$string['ignored_modules_help'] = 'Selecione os módulos que não serão mostrados nos relatórios. Utilize a tecla Ctrl para selecionar mais de um ou para remover a seleção.';
$string['user_filter_help'] = 'Define regras para filtrar os usuários presentes no relatório. Veja a ajuda para mais informações.';
$string['select_course_help'] = 'Selecione o curso que será utilizado para gerar o relatório.';
$string['report_options_help'] = 'Selecione as opções que serão ativadas na geração do relatório. Utilize a tecla Ctrl para selecionar mais de um ou para remover a seleção.';
$string['user_role_help'] = 'Selecione os perfil de usuário que serão mostrados nos relatórios. Utilize a tecla Ctrl para selecionar mais de um ou para remover a seleção.';
$string['feedback_passed_help'] = 'Informe o texto que será mostrado para os usuários que atingiram anota mínima no total do curso.';
$string['feedback_failed_help'] = 'Informe o texto que será mostrado para os usuários que atingiram anota mínima no total do curso.';
$string['minimum_grade_help'] = 'Informe a nota mínima para o usuário ser considerado aprovado (em ralação ao total do curso).';

/**
 * Opções disponíveis nos campos de formulário
 */
$string['hide_deleted'] = 'Não mostrar usuários definidos como apagados no banco.';
$string['hide_suspended'] = 'Não mostrar usuários suspensos no Moodle.';
$string['hide_canceled_enrol'] = 'Mostrar somente usuários com inscrição ativa no curso.';
$string['hide_header'] = 'Não mostrar cabeçalho do curso (sessão 0).';
$string['hide_section'] = 'Não mostrar sessões e módulos ocultos.';
$string['show_access'] = 'Mostrar dados de acesso ao curso.';
$string['only_passed'] = 'Mostrar somente usuários que atingiram a nota mínima no total do curso.';
$string['only_failed'] = 'Mostrar somente usuários que não atingiram a nota mínima no total do curso.';
$string['show_feedback'] = 'Mostrar feedback (aprovado / reprovado).';

/**
 * Textos diversos
 */
$string['error_update_config'] = 'Ocorreu um erro gravando as opções dos relatórios. Tente novamente mais trade';
$string['save'] = 'Salvar';
$string['header_definitions'] = 'Definições';
$string['generate_report'] = 'Gerar relatório';
$string['default_no_registry'] = '';
$string['course_access_data'] = 'Dados de acesso ao curso';
$string['first'] = 'Primeiro';
$string['last'] = 'Último';
$string['amount'] = 'Quantidade';
$string['not_completed'] = 'Não';
$string['completed'] = 'Sim';
$string['completed_passed'] = 'Sim (obteve nota)';
$string['completed_failed'] = 'Sim (não obteve nota)';
$string['not_viewed'] = 'Não';
$string['viewed'] = 'Sim';
$string['null_viewed'] = 'Visualização não monitorada';
$string['tbl_empty_complete'] = 'Não';
$string['tbl_empty_visualized'] = 'Não';
$string['tbl_empty_data'] = '';
$string['hidden'] = 'Oculto';
$string['concluded'] = 'Concluído?';
$string['visualized'] = 'Visualizado?';
$string['date'] = 'Data';
$string['final_grade'] = 'TOTAL DO CURSO';
$string['no_data'] = 'Sem dados para calcular a estimativa';
$string['estimate_time'] = 'Tempo (estimativa)';
$string['feedback_text'] = 'SITUAÇÃO';

/**
 * Mensagens
 */
$string['course_required'] = 'É necessário selecionar um curso.';
$string['role_required'] = 'É necessário selecionar ao menos um perfil de usuário.';
$string['no_extra_fields'] = 'Não foi encontrado nenhum campo extra na plataforma';

/**
 * Campos da tabela de usuários
 */
$string['id'] = 'ID';
$string['auth'] = 'Método de autenticação';
$string['confirmed'] = 'Inscrição confirmada';
$string['policyagreed'] = 'Política aceita';
$string['deleted'] = 'Conta apagada';
$string['suspended'] = 'Consta suspensa';
$string['username'] = 'Nome de usuário';
$string['idnumber'] = 'Número de identificação';
$string['firstname'] = 'Primeiro Nome';
$string['lastname'] = 'Ultimo Nome';
$string['email'] = 'E-mail';
$string['phone1'] = 'Telefone 1';
$string['phone2'] = 'Telefone 2';
$string['institution'] = 'Instituição';
$string['department'] = 'Departamento';
$string['address'] = 'Endereço';
$string['city'] = 'Cidade';
$string['country'] = 'País';
$string['firstlogin'] = 'Primeiro login';
$string['lang'] = 'Idioma';
$string['lastlogin'] = 'Último login';
$string['currentlogin'] = 'Login atual';
$string['description'] = 'Descrição';
$string['timecreated'] = 'Data de criação';
$string['timemodified'] = 'Última modificação';
$string['firstaccess'] = 'Primeiro acesso';
$string['lastaccess'] = 'Último acesso';
$string['lastip'] = 'Último IP registrado';
$string['icq'] = 'ICQ';
$string['skype'] = 'Skype';
$string['Yahoo'] = 'Yahoo';
$string['aim'] = 'AIM';
$string['msn'] = 'MSN';
$string['url'] = 'URL';
