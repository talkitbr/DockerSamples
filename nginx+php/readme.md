Neste tutorial demonstrarei um passo a passo de como preparar um ambiente de desenvolvimento com nginx e PHP usando Docker no Windows 10. O tutorial faz parte de uma série de artigos que publiquei sobre este assunto no blog do TalkitBR.

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_3_compose.png" target="_blank"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_3_compose.png?w=300" alt="docker_3_compose" width="300" height="295" class="alignright size-medium wp-image-9128" /></a>

Vamos ver aqui como realizar composição de containers Docker que interagem entre si. O tutorial foi baseado <a href="http://www.penta-code.com/creating-a-lemp-stack-with-docker-compose/" target="_blank">no post de YONGZHI HUANG que demonstra como criar LEMP stack no Docker.</a>

A composição de containers no Docker é feita usando o comando <code>docker-compose</code> que permite definir e rodar aplicações que envolvem múltiplos containers, com compartilhamento de recursos entre eles e também outras funcionaldiades como a escalabilidade.

<blockquote>Se desejar, você pode baixar o conteúdo completo que está disponível nesta mesma pasta.</blockquote>

### Definindo o servidor nginx

<ol>
<li>Vamos começar criando uma pasta para manter nossa aplicação além do arquivo que irá descrever a composição de containers. No meu caso irei criar a pasta em C:\docker\lemp</li>

<li>Abrir essa pasta em um editor de arquivos. Aqui irei usar o <a href="/2016/08/18/consolidando-visual-studio-code/" target="_blank">Visual Studio Code</a> que extensões que permitem editar nosso arquivo docker-compose.yml.</li>

<li>Para facilitar a edição, vou usar uma extensão do Visual Studio Code. Acesse as extensões e então busque por "docker compose". Selecione a extensão "Dockerfile and Docker Compose File (yml)" e faça a instalação. Será solicitado o reinício do Visual Studio Code quando a instalação terminar.

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_3_vscode_extension.png"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_3_vscode_extension.png?w=723" alt="docker_3_vscode_extension" width="723" height="426" class="aligncenter size-large wp-image-9131" /></a></li>

<li>Voltando para o Explorer do Visual Studio Code, crie o arquivo <code>docker-compose.yml</code>. Nele vamos incluir as imagens que iremos usar. Vamos começar com a imagem <a href="https://hub.docker.com/r/tutum/nginx/" target="_blank">tutum/nginx</a>:

<code language="javascript">
nginx:
    image: tutum/nginx
    ports:
        - "8080:80"
    volumes: 
        - ./logs/nginx-error.log:/var/log/nginx/error.log
        - ./logs/nginx-access.log:/var/log/nginx/access.log
</code>

<blockquote>Note que estamos mapeando a porta 8080 de nossa máquina para a porta 80 do container. Além disso, estamos mapeando dois arquivos locais (<code>nginx-error.log</code> e <code>nginx-access.log</code> para conseguir obter informações de log do servidor, já que esses arquivos são os usados por default pelo nginx para escrever os logs.</blockquote></li>

<li>Agora vamos criar esses dois arquivos dentro da pasta <code>logs</code>. Crie a pasta <code>logs</code> na pasta raiz e, dentro desta pasta, os arquivos <code>nginx-error.log</code> e <code>nginx-access.log</code>.</li>

<li>Usando o próprio Visual Studio Code, abra o menu de contexto do arquivo (clique com o botão direito do mouse sobre o arquivo) e selecione a opção "Open in Command Prompt".

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_3_vscode_dockercompose_12.png" target="_blank"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_3_vscode_dockercompose_12.png" alt="docker_3_vscode_dockercompose_1" width="723" height="401" class="aligncenter size-full wp-image-9139" /></a></li>

<li>
Já no prompt de comando, execute o comando <code>docker-compose up -d</code>. Esse comando já fará o download (pull) da imagem do tutum/nginx, se ele ainda não estiver presente localmente, e depois irá iniciar o Container. 

<pre style="-moz-border-radius:5px;-webkit-border-radius:5px;background-color:#202020;border:4px solid silver;border-radius:5px;box-shadow:2px 2px 3px #6e6e6e;color:#e2e2e2;display:block;font:.5em 'andale mono', 'lucida console', monospace;line-height:1em;overflow:auto;padding:15px;margin-bottom:10px;">
C:\docker\lemp&gt;docker-compose up -d
Creating lemp_nginx_1

C:\docker\lemp&gt;</pre>

<blockquote>Um erro comum que pode ocorrer aqui é <code>Bind for 0.0.0.0:8080 failed</code> (será o caso de quem acabou de seguir os passos do artigo anterior). Esse erro ocorre quando já temos alguma aplicação usando a porta TCP que fornecemos (nesse caso a porta 8080). E essa aplicação pode inclusive ser um container docker. Para verificar os containers docker em execução, execute o comando <code>docker ps</code> e para parar um container que está em execução, execute o comando <code>docker stop</code> fornecendo o nome ou ID do container. Depois de liberada a porta 8080, tente executar novamente o comando <code>docker-compose up -d</code>.</blockquote>

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_3_dockercompose_up_error.png" target="_blank"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_3_dockercompose_up_error.png" alt="docker_3_dockercompose_up_error" width="723" height="305" class="aligncenter size-full wp-image-9134" /></a>
</li>
</ol>

Pronto, agora o container já está no ar e podemos testá-lo acessando <code>http://localhost:8080</code> ou ainda <code>http://127.0.0.1:8080</code>.

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_3_browser_ninx_1.png" target="_blank"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_3_browser_ninx_1.png" alt="docker_3_browser_ninx_1" width="667" height="350" class="aligncenter size-full wp-image-9135" /></a>

E se você abrir o arquivo <code>./logs/nginx-access.log</code>, irá notar que o servidor nginx gravou informações de log no arquivo.

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_3_nginxlog.png"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_3_nginxlog.png" alt="docker_3_nginxlog" width="723" height="269" class="aligncenter size-full wp-image-9142" /></a>

### Adicionando container PHP e criando o index.php

Agora vamos começar a usar outros recursos do docker-composer como a definição de outros containers e link entres containers. Nesse caso, vamos incluir o PHP no servidor nginx que acabamos de criar. Além disso, vamos compartilhar uma pasta local da nossa máquina para escrevermos nosso conteúdo PHP.

<ol>
<li>Inclua a definição do novo container php no final do mesmo arquivo docker-compose.yml:

<code language="javascript">
phpfpm:
    image: php:fpm
    ports:
        - "9000:9000"
    volumes:
        - ./public:/usr/share/nginx/html
</code>
<blockquote>Notem que compartilhamos a pasta local <code>public</code> na pasta <code>/usr/share/nginx/html</code> do container.</blockquote>
</li>

<li>Crie a pasta public na raiz da pasta aberta no Visual Studio Code e dentro dela o arquivo index php a seguir:

<code language="php">
<?php
phpinfo();
</code>

</li>
<li>De volta no arquivo <code>docker-composer.yml</code> e inclua, no container nginx, o link para o container phpfpm e dois novos volumes compartilhando pastas locais onde iremos incluir arquivo de configuração do servidor (necessário para configurar o PHP no nginx):

<code language="javascript">
nginx:
    image: tutum/nginx
    ports:
        - "8080:80"
    links:
        - phpfpm
    volumes: 
        - ./logs/nginx-error.log:/var/log/nginx/error.log
        - ./logs/nginx-access.log:/var/log/nginx/access.log
        - ./nginx/default:/etc/nginx/sites-available/default
        - ./nginx/default:/etc/nginx/sites-enabled/default
</code>

<blockquote>O link é que permite a comunicação entre os dois containers. Se não for especificado, os container não irão enxergar um ao outro.</blockquote>
</li>
<li>Agora vamos adicionar o arquivo de configuração do nginx. Crie a pasta <code>nginx</code> na raiz da sua pasta aberta no Visual Studio Code. Dentro da pasta <code>nginx</code> crie o arquivo <code>default</code> com o seguinte conteúdo:

<code language="javascript">
server {
    listen  80;

    # this path MUST be exactly as docker-compose.fpm.volumes,
    # even if it doesn't exists in this dock.
    root /usr/share/nginx/html;
    index index.php index.html index.html;
    
    server_name 10.0.75.2;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass phpfpm:9000; 
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
</code>
<blockquote>
<strong>Notas:</strong>
<ul>
<li>Nesta configuração estamos definindo a pasta raiz do servidor (root), os arquivos de boas vindas (index), o nome do servidor (server_name), como trataremos arquivos abertos na raiz (location /) e como iremos tratar arquivos .php (location ~ \.php$).</li>
<li>O server_name especificado corresponde ao IP do nosso host Docker. É possível obter esse endereço através dos comandos docker-machine ls (para listar os hosts docker) e então <code>docker-machine ip [nome_docker_host]</code>.

<h2><pre style="-moz-border-radius:5px;-webkit-border-radius:5px;background-color:#202020;border:4px solid silver;border-radius:5px;box-shadow:2px 2px 3px #6e6e6e;color:#e2e2e2;display:block;font:.5em 'andale mono', 'lucida console', monospace;line-height:1em;overflow:auto;padding:15px;margin-bottom:10px;">
c:\&gt;docker-machine ls
NAME          ACTIVE   DRIVER   STATE     URL                    SWARM   DOCKER    ERRORS
MobyLinuxVM   -        hyperv   Running   tcp://10.0.75.2:2376           Unknown   Unable to query docker version: Get https://10.0.75.2:2376/v1.15/version: dial tcp 10.0.75.2:2376: connectex: No connection could be made because the target machine actively refused it.
vm            -        hyperv                                            Unknown

c:\&gt;docker-machine ip MobyLinuxVM
10.0.75.2

c:\&gt;</pre></h2>
</li>
</ul>
</blockquote>
</li>
</ol>

Vamos agora atualizar nossos containers no docker. Acesse novamente o prompt de comando na pasta do nosso projeto e então execute o comando <code>docker-compose up -d</code>.

<h2><pre style="-moz-border-radius:5px;-webkit-border-radius:5px;background-color:#202020;border:4px solid silver;border-radius:5px;box-shadow:2px 2px 3px #6e6e6e;color:#e2e2e2;display:block;font:.5em 'andale mono', 'lucida console', monospace;line-height:1em;overflow:auto;padding:15px;margin-bottom:10px;">
C:\docker\lemp&gt;docker-compose up -d
Creating lemp_phpfpm_1
Recreating lemp_nginx_1

C:\docker\lemp&gt;</pre></h2>
<blockquote>Notem que foi criado o container lemp_phpfpm_1. Já o container lemp_nginx_1 foi recriado pois modificamos suas configurações.</blockquote>

Pronto, os containers já estão no ar e podemos testá-los acessando <code>http://localhost:8080/index.php</code> ou ainda <code>http://127.0.0.1:8080/index.php</code>:

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_3_browser_php.png" target="_blank"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_3_browser_php.png" alt="docker_3_browser_php" width="667" height="350" class="aligncenter size-full wp-image-9150" /></a>

<h3>Próximos Passos</h3>
Apresentei nesse tutorial como criar e configurar um servidor nginx para publicar arquivos PHP. Contudo tivemos que incluir outro container PHP para disponibilizar a engine PHP para o nginx.

E fiquem a vontade para sugerir exemplos para demonstrarmos aqui. Abraços e até a próxima.