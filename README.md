# Moodle Monitoring

## Intro
Monitoring plugin for Moodle. Allows you to extract custom reports on the platform.

This plugin was based on several scripts that I used to meet the demands of the company where I work.

## Important
This plugin requires PHP version 7.3.0 or higher to work.

## Version history

### 1.0.0
- MATURITY: BETA
- Implementation of basic features for plugin installation
- Implementation of the configuration screen presented during installation
- Pre-configured plugin homepage
- Implemented the plugin homepage, where it is possible to change the general settings
- Implementation of the participation report
- Implementation of the grade report
- Implementation of the dedication report
- Implementation of the help tab
- Code review

### 1.0.1
- MATURITY: BETA
- Refactored code
- Removed post installation configuration screen
- Extra filter option for users moved to configuration page
- Option to filter grade report results on pass or fail users
- Option to set the grade for passing the course

### 1.0.2
- MATURITY: BETA
- Bugs fixed
- 
### 1.0.3
- MATURITY: BETA
- Bugs fixed

## Known bugs
- Error generating reports using custom fields. Expected fix for version 1.0.4 on 9/1/2022

## Install

### Método 1: usando a interface web
- Acesse o Moodle como administrador.
- Acesse Administração do site > Plugins > Instalar plugins.
- Selecione o arquivo do plugin e clique em "Instalar plugin do arquivo zip"
- Siga os passos na tela até a finalização do processo.

### Método 2: realizando o upload do plugin
- Extraia o conteúdo do arquivo zip e suba seu conteúdo para a pasta "report".
- A estrutura deve ficar "report/monitoring".
- Acesse o Moodle como administrador.
- Acesse a área de notificação do Moodle para realizar a instalação.

### Method 1: Using the web interface
- Log in to Moodle as an administrator.
- Go to Site Administration > Plugins > Install Plugins.
- Select the plugin file and click "Install plugin from zip file"
- Follow the on-screen steps until the process is complete.

### Method 2: uploading the plugin
- Extract the contents of the zip file and upload its contents to the "report" folder.
- The structure should be "report/monitoring".
- Log in to Moodle as an administrator.
- Access the Moodle notification area to perform the installation.
