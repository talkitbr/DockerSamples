O conteúdo disponível nesta pasta é parte de uma série de artigos em que descrevo como desenvolver no Windows 10 usando Docker. <a href="https://talkitbr.com/?s=%22Usando+Docker+no+Windows+10%22" target="_blank">Clique aqui para acessar os artigos</a>.

Neste tutorial demonstrarei um passo a passo de como preparar um ambiente de desenvolvimento LEMP (Linux/Nginx/MySSQL/PHP) usando Docker no Windows 10. O tutorial faz parte de uma série de artigos que publiquei sobre este assunto no blog do TalkitBR e foi baseado <a href="http://www.penta-code.com/creating-a-lemp-stack-with-docker-compose/" target="_blank">no post de YONGZHI HUANG que demonstra como criar LEMP stack no Docker.</a>

O que apresento aqui é uma continuidade de outro tutorial que demonstra como criar uma composição de containers Nginx/PHP disponível neste <a href="../nginx%2Bphp" target="_blank">link</a>. Recomenda-se fazer o outro tutorial antes de iniciar este.

<blockquote style="clear:both">Se desejar, você pode baixar o conteúdo completo que está disponível nesta mesma pasta.</blockquote>

## Definindo o Banco de Dados

<ol>
 	<li>Abra a pasta onde está o nosso conteúdo desenvolvido no artigo anterior. No meu caso é a pasta em <code>C:\docker\lemp</code></li>
 	<li>No Explorer do Visual Studio Code, abra o arquivo <code>docker-compose.yml</code>. Nele vamos incluir a imagem <a href="https://hub.docker.com/_/mariadb/" target="_blank">MariaDB</a> que iremos usar. Vamos começar com a imagem <a href="https://hub.docker.com/r/tutum/nginx/" target="_blank">tutum/nginx</a>:

<pre><code language="Dockerfile">
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

phpfpm:
    image: php:fpm
    ports:
        - "9000:9000"
    volumes:
        - ./public:/usr/share/nginx/html

mysql:
    image: mariadb
    ports: 
        - 3306:3306
    environment:
        MYSQL_ROOT_PASSWORD: admin
</code></pre>

<blockquote><strong>Notas: </strong><ul><li>Usamos o recurso <code>environment</code> para especificar uma variável de ambiente no nosso container. No caso especificamos a senha do usuário ROOT. A porta que iremos usar é a mesma 3306 padrão do MySQL.</li><li>Até aqui já temos o suficiente para acessar o Banco de Dados utilizando qualquer ferramenta cliente do mercado. Basta para nós acessar o endereço http://localhost:3306 ou ainda http://127.0.0.1:3306.</li></ul></blockquote> 
</li>
<li>Vamos incluir ainda definição de outro container que será nosso PHPMyAdmin para acessar o servidor de banco de dados. Adicione a seguinte definição de container no final do arquivo <code>docker-compose.yml</code>:

<pre><code language="Dockerfile">
phpmyadmin:
    image: phpmyadmin/phpmyadmin
    restart: always
    links:
        - mysql:db
    ports:
        - 8183:80
    environment:
        MYSQL_USERNAME: root
        MYSQL_ROOT_PASSWORD: admin    
        PMA_ARBITRARY: 0
</code></pre>

<blockquote><strong>Notas: </strong><ul><li>Especificamos um link para o container mysql</li><li>Expomos o PHPMyAdmin na porta 8183 da nossa máquina local.</li><li>Definimos o usuário e senha para acesso ao servidor de banco de dados usando variáveis de ambiente.</li><li>Incluímos a variavel <a href="http://www.pinksterfeesten.info/phpmyadmin/doc/html/setup.html" target="_blank">PMA_ARBITRARY</a> com valor 0 para indicar que não vamos permitir fornecer o endereço do servidor de banco de dados no formulário de login (será usado o padrão localhost).</li></ul></blockquote> 
</li>
<li>
Vamos agora atualizar nossos containers no docker. Acesse novamente o prompt de comando a partir da pasta do nosso projeto e então execute o comando <code>docker-compose up -d</code>.

<pre style="-moz-border-radius:5px;-webkit-border-radius:5px;background-color:#202020;border:4px solid silver;border-radius:5px;box-shadow:2px 2px 3px #6e6e6e;color:#e2e2e2;display:block;font:1em 'andale mono', 'lucida console', monospace;line-height:1em;overflow:auto;padding:15px;margin-bottom:10px;">C:\docker\lemp&gt;docker-compose up -d
Creating lemp_mysql_1
Creating lemp_phpmyadmin_1
lemp_phpfpm_1 is up-to-date
lemp_nginx_1 is up-to-date

C:\docker\lemp&gt;</pre>
<blockquote>Notem que os containers lemp_phpfpm_1 e lemp_nginx_1 não foram atualizados pois não fizemos modificação alguma neles. Já o lemp_mysql_1 e o lemp_phpmyadmin_1 foram criados e iniciados.</blockquote>
</li>
<li>
Pronto, os containers já estão no ar e podemos testar o PHPMyAdmin acessando <code>http://localhost:8183/</code> ou ainda <code>http://127.0.0.1:8183/index.php</code> e usar o usuário <em>ROOT</em> com senha <em>admin</em> para acessar o servidor de banco de dados:

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_4_phpmyadmin_mysql.png"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_4_phpmyadmin_mysql.png" alt="docker_4_phpmyadmin_mysql" width="723" height="436" class="aligncenter size-full wp-image-9163" /></a>
</li>
<li>Vamos criar nosso banco de dados. Ainda no PHPMyAdmin, acesse a aba SQL e especifique o seguinte código SQL e então execute o código (botão "Go"):
<pre><code language="sql">
CREATE DATABASE docker_sample;

USE docker_sample;

CREATE TABLE users ( 
    `id` INT NOT NULL AUTO_INCREMENT, 
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

INSERT INTO users (name, email) values ('joao', 'joao@email.com') ;
INSERT INTO users (name, email) values ('maria', 'maria@email.com');
</code></pre>

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_4_phpmyadmin_newdb1.png" target="_blank"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_4_phpmyadmin_newdb1.png" alt="docker_4_phpmyadmin_newdb" width="723" height="535" class="aligncenter size-full wp-image-9166" /></a>

Como resultado, o PHPMyAdmin deve informar que o banco de dados foi criado com sucesso, criar a tabela <code>users</code> e inserir os usuários "joao" e "maria".
</li>
</ol>

### Acessando o banco de dados do container PHP
 
Para acessarmos o banco de dados MySQL a partir do PHP, precisamos adicionar uma extensãoque pode ser tanto o <a href="http://php.net/manual/pt_BR/book.mysqli.php" target="_blank">MySQLi</a> como o <a href="http://php.net/manual/pt_BR/book.pdo.php" target="_blank">PDO</a>. Neste artigo irei usar o MySQLi. Mas para isso teremos que modificar o container do PHP para incluir essa extensão.

Para fazer isso, vamos usar um outro recurso do Docker que é a opção de redefinirmos localmente uma imagem através do arquivo Dockerfile. Essse arquivo define a imagem de origem e permite acrescentar acrescentar customizações próprias como arquivos e comandos que serão executados na imagem original. Depois disso basta fazermos o <code>docker build</code> para construir a nova imagem e depois usá-la para definirmos novos containers.

<ol>
<li>Vamos criar o arquivo <code>Dockerfile</code> na raiz da nossa pasta do projeto no Visual Studio Code:
<pre><code language="Dockerfile">
FROM php:fpm

RUN apt-get update && \
    apt-get install vim git -y
RUN docker-php-ext-install mysqli
</code></pre>

<blockquote>Estamos usando a mesma imagem de origem php:fpm, mas agora estamos instalando a extensão mysqli.</blockquote>
</li>
<li>Voltando para o arquivo <code>docker-compose.yml</code>, vamos alterar a definição do container phpfm para usar o Dockerfile ao invés da imagem original:

<pre><code language="Dockerfile">
phpfpm:
    dockerfile: Dockerfile.mysql
    build: ./
    ports:
        - "9000:9000"
    volumes:
        - ./public:/usr/share/nginx/html
</code></pre>

<blockquote>Note que além de definir o dockerfile, especificamos o build para gerar a imagem do container.</blockquote>
</li>
<li>Agora podemos alterar o arquivo <code>public/index.php</code> para configurar o acesso ao servidor de banco de dados e usar os recursos do banco:

<pre><code language="php">
&lt;?php

$rows = array();
    
// Try and connect to the database
$connection = mysqli_connect("10.0.75.2:3306", "root", "admin", "docker_sample");

// If connection was not successful, handle the error
if($connection === false) {
    echo "Unable to connect to mysql.";
}
else {
    $result = mysqli_query($connection,"select * from users");
    
    // Fetch all the rows in an array
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

?&gt;

&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;title&gt;PHP Docker Sample&lt;/title&gt;
        &lt;meta charset="utf-8"&gt;
    &lt;/head&gt;
    &lt;body&gt;

        &lt;?php if (count($rows) == 0) { ?&gt;
            &lt;h1&gt;No user has been registered yet.&lt;/h1&gt;
        &lt;?php } else { ?&gt;

            &lt;table border="1"&gt;
                &lt;tr&gt;
                    &lt;th&gt;Id&lt;/th&gt;
                    &lt;th&gt;Name&lt;/th&gt;
                    &lt;th&gt;Email&lt;/th&gt;
                &lt;/tr&gt;
            
            &lt;?php foreach ($rows as &$row) { ?&gt;

                &lt;tr&gt;
                    &lt;td&gt;&lt;?= $row['id'] ?&gt;&lt;/td&gt;
                    &lt;td&gt;&lt;?= $row['name'] ?&gt;&lt;/td&gt;
                    &lt;td&gt;&lt;?= $row['email'] ?&gt;&lt;/td&gt;
                &lt;/tr&gt;
            
            &lt;? } ?&gt;
            &lt;/table&gt;

        &lt;?php }?&gt;
    &lt;/body&gt;
&lt;/html&gt;
</code></pre>

<blockquote>O objetivo deste código é apenas demonstrar o acesso ao servidor de banco de dados usando PHP.</blockquote>
</li>
<li>

Vamos atualizar de novo os nossos containers no docker. Usando o prompt de comando a partir da pasta do nosso projeto, execute o comando <code>docker-compose up -d</code>.
<pre style="-moz-border-radius:5px;-webkit-border-radius:5px;background-color:#202020;border:4px solid silver;border-radius:5px;box-shadow:2px 2px 3px #6e6e6e;color:#e2e2e2;display:block;font:1em 'andale mono', 'lucida console', monospace;line-height:1em;overflow:auto;padding:15px;margin-bottom:10px;">C:\docker\lemp&gt;docker-compose up -d
Recreating lemp_phpfpm_1
lemp_mysql_1 is up-to-date
lemp_phpmyadmin_1 is up-to-date
Recreating lemp_nginx_1

C:\docker\lemp&gt;</pre>
<blockquote>Notem que os containers lemp_phpfpm_1 e lemp_nginx_1 foram recriados. Isso porque nossa modificação afetou tanto o container do php como também o container do nginx, pois este tem um link com o container do php.</blockquote>
</li>
<li>Agora acesse novamente o endereço http://localhost:8080 ou ainda http://127.0.0.1:8080 para visualizar o novo conteúdo PHP acessando nosso banco de dados que criamos a pouco:

<a href="https://talkitbr.files.wordpress.com/2016/10/docker_4_mysql_access_php.png" target="_blank"><img src="https://talkitbr.files.wordpress.com/2016/10/docker_4_mysql_access_php.png" alt="docker_4_mysql_access_php" width="654" height="271" class="aligncenter size-full wp-image-9167" /></a>
</li>
</ol>

<h2>Próximos Passos</h2>
Apresentei nesse tutorial como criar e configurar um ambiente de desenvolvimento LEMP, mostrando como criar um banco de dados e usá-lo dentro do container PHP.

Fiquem a vontade para sugerir exemplos para demonstrarmos aqui. Abraços e até a próxima.