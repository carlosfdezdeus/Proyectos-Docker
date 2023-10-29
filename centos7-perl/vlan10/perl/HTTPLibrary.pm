package HTTPLibrary;

use strict;
use warnings;

use HTTP::Request;
use HTTP::Headers;
use LWPx::ParanoidAgent;
use LWP::UserAgent;
#use Data::Dumper;


# Creaci�n autom�tica de la cabecera User-Agent:
my $agent = 'Configurator 1.40 UA';
my $user_agent = new LWPx::ParanoidAgent(agent=>$agent) || die "CANNOT POST";

sub login_post_request {
    #Argumentos:
    my ($url, $password, $user) = @_;    
    #print "URL: $url\n";
    #print "Usuario: $user\n";
    #print "Contraseña cifrada: $password\n";

    # C�digo:
    my $json =  '{"action":"set_web_user_login","args":{"user":"'.$user.'","password":"'.$password.'"},"token":""}';

    my $content_length = length($json);

    my $headers = HTTP::Headers->new(
        'Accept' => 'application/json, text/javascript, */*; q=0.01',
        'Accept-Encoding' => 'gzip, deflate',
        'Accept-Language' => 'es-ES,es;q=0.9',
        'Connection' => 'keep-alive',
        'Content-Length' => $content_length,
        'Content-Type' => 'json',
        'Cookie' => 'CGISID=NULL',
        'Host' => '192.168.8.1',    #Igual hay que comentarlo
        'Origin' => 'http://192.168.8.1',
        'Referer' => 'http://192.168.8.1/index.html',
        'User-Agent' => $user_agent,
        'X-Requested-With' => 'XMLHttpRequest',
    );

    my $request = HTTP::Request->new('POST', $url, $headers, $json);
    
    my $ua = LWP::UserAgent->new;
    my $response = $ua->request($request);

    if ($response->is_success) {
        #print "\nRespuesta: \n";        
        #print Dumper $response;<STDIN>;
        return $response;    # Para acceder a una parte de la response: $response->{content};
    } else {
        die "Error en la solicitud: $response->status_line $response->reason_line\n";
    }
}

sub config_post_request {
    #Argumentos:
    my ($url, $token, $set_cookie) = @_;    
    #print "URL: $url\n";
    #print "Token: $token\n";
    if ($set_cookie=~/(CGISID=.+);/){
        $set_cookie=$1;
        #print "Set-Cookie: $set_cookie\n";
    }

    my $json =  '{"action":"get_system_config","token":"'.$token.'"}';

    my $content_length = length($json);

    my $headers = HTTP::Headers->new(
        #'Accept' => 'application/json, text/javascript, */*; q=0.01',
        #'Accept-Encoding' => 'gzip, deflate',
        #'Accept-Language' => 'es-ES,es;q=0.9',
        #'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
        'Content-Length' => $content_length,
        'Content-Type' => 'json',
        'Cookie' => $set_cookie,
        'Host' => '192.168.8.1',
        #'Origin' => 'http://192.168.8.1',
        'Referer' => 'http://192.168.8.1/system/deviceInformation.html?clearcache=true',
        'User-Agent' => $user_agent,
        #'X-Requested-With' => 'XMLHttpRequest',
    );
    
    my $request = HTTP::Request->new('POST', $url, $headers, $json);

    my $ua = LWP::UserAgent->new;
    my $response = $ua->request($request);

    if ($response->is_success) {
        #print Dumper $headers;<STDIN>;
        #print "\nRespuesta: \n";        
        #print Dumper $response->decoded_content;<STDIN>;
        return $response->decoded_content;    # Para acceder a una parte de la response: $response->{content};
    } else {
        die "Error en la solicitud: $response->{status} $response->{reason}\n";
    }
}
1;