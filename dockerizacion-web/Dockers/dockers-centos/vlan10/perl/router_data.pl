#! /usr/bin/perl
use strict;
use warnings;

use JSON::MaybeXS;              # Para el uso de decode_json => coger el token

use lib '.';
use AESLibrary;
use HTTPLibrary;

my $login_url = "http://192.168.8.1/cgi-bin/login.cgi";
my $config_url = "http://192.168.8.1/cgi-bin/gui.cgi";

my $user = shift @ARGV || die "Falta el valor del usuario.\n";
my $password = shift @ARGV || die "Falta el valor de la contraseña.\n";

my $password_cifrado = AESLibrary::encode($password);

# Hago el login del router:
my $login_response = HTTPLibrary::login_post_request($login_url, $password_cifrado, $user);

# Cojo el parametro cgitoken de la response porque lo necesito para hacer el POST para coger los datos del sistema.
my $response_content = $login_response->decoded_content;
my $response_data = JSON::MaybeXS->new->decode($response_content);
my $cgitoken_value = $response_data->{'set_web_user_login'}{'cgitoken'};

# Cojo el parámetro CGISID de las cookies de la response porque lo necesito para hacer el POST para coger los datos del sistema.
my $set_cookie = $login_response->header('Set-Cookie');

# Obtengo los datos del router
my $config_response = HTTPLibrary::config_post_request($config_url, $cgitoken_value, $set_cookie);

my $config_data = JSON::MaybeXS->new->decode($config_response);
# Obtengo los datos del router
my $system_config = $config_data->{'get_system_config'};
my $imei = $system_config->{'IMEI'};
my $etc_version = $system_config->{'etc_version'};
my $mdm_version = $system_config->{'mdm_version'};
my $hw_version = $system_config->{'hw_version'};
my $eth_mac = $system_config->{'eth_mac'};

# Imprimo los datos del router
print "IMEI: $imei\n";
print "Versión de software: $etc_version\n";
print "MPSS: $mdm_version\n";
print "Versión de hardware: $hw_version\n";
print "Dirección MAC LAN: $eth_mac\n";