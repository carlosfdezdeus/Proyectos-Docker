#!/usr/bin/perl
package AESLibrary;

use strict;
use warnings;


# ---------------------------------- VARIABLES --------------------------------------#
our @AES_Sbox = (99, 124, 119, 123, 242, 107, 111, 197, 48, 1, 103, 43, 254, 215, 171,
    118, 202, 130, 201, 125, 250, 89, 71, 240, 173, 212, 162, 175, 156, 164, 114, 192, 183, 253,
    147, 38, 54, 63, 247, 204, 52, 165, 229, 241, 113, 216, 49, 21, 4, 199, 35, 195, 24, 150, 5, 154,
    7, 18, 128, 226, 235, 39, 178, 117, 9, 131, 44, 26, 27, 110, 90, 160, 82, 59, 214, 179, 41, 227,
    47, 132, 83, 209, 0, 237, 32, 252, 177, 91, 106, 203, 190, 57, 74, 76, 88, 207, 208, 239, 170,
    251, 67, 77, 51, 133, 69, 249, 2, 127, 80, 60, 159, 168, 81, 163, 64, 143, 146, 157, 56, 245,
    188, 182, 218, 33, 16, 255, 243, 210, 205, 12, 19, 236, 95, 151, 68, 23, 196, 167, 126, 61,
    100, 93, 25, 115, 96, 129, 79, 220, 34, 42, 144, 136, 70, 238, 184, 20, 222, 94, 11, 219, 224,
    50, 58, 10, 73, 6, 36, 92, 194, 211, 172, 98, 145, 149, 228, 121, 231, 200, 55, 109, 141, 213,
    78, 169, 108, 86, 244, 234, 101, 122, 174, 8, 186, 120, 37, 46, 28, 166, 180, 198, 232, 221,
    116, 31, 75, 189, 139, 138, 112, 62, 181, 102, 72, 3, 246, 14, 97, 53, 87, 185, 134, 193, 29,
    158, 225, 248, 152, 17, 105, 217, 142, 148, 155, 30, 135, 233, 206, 85, 40, 223, 140, 161,
    137, 13, 191, 230, 66, 104, 65, 153, 45, 15, 176, 84, 187, 22);

our @AES_ShiftRowTab = (0, 5, 10, 15, 4, 9, 14, 3, 8, 13, 2, 7, 12, 1, 6, 11);
our @AES_xtime = ();

# ---------------------------------- FUNCIONES --------------------------------------#
sub AES_Init {
    @AES_xtime = ();
    for my $i (0 .. 255) {
        $AES_xtime[$i] = ($i << 1) ^ (($i & 0x80) ? 0x1b : 0);
    }
}

sub AES_AddRoundKey {
    my ($state_ref, $rkey_ref) = @_;
    for my $i (0 .. 15) {
        $state_ref->[$i] ^= $rkey_ref->[$i];
    }
}

sub AES_SubBytes {
    my ($state_ref, $sbox_ref) = @_;
    for my $i (0 .. 15) {
        $state_ref->[$i] = $sbox_ref->[$state_ref->[$i]];
    }
}

sub AES_ShiftRows {
    my ($state_ref, $shifttab_ref) = @_;
    my @h = @$state_ref;
    for my $i (0 .. 15) {
        $state_ref->[$i] = $h[$shifttab_ref->[$i]];
    }
}

sub AES_MixColumns {
    my ($state_ref) = @_;
    for (my $i = 0; $i < 16; $i += 4) {
        my $s0 = $state_ref->[$i + 0];
        my $s1 = $state_ref->[$i + 1];
        my $s2 = $state_ref->[$i + 2];
        my $s3 = $state_ref->[$i + 3];
        my $h = $s0 ^ $s1 ^ $s2 ^ $s3;

        $state_ref->[$i + 0] ^= $h ^ $AES_xtime[$s0 ^ $s1];
        $state_ref->[$i + 1] ^= $h ^ $AES_xtime[$s1 ^ $s2];
        $state_ref->[$i + 2] ^= $h ^ $AES_xtime[$s2 ^ $s3];
        $state_ref->[$i + 3] ^= $h ^ $AES_xtime[$s3 ^ $s0];
    }
}

sub AES_Done {
    @AES_xtime = ();
}

sub AES_Encrypt {
    #Argumentos
    my ($block_ref, $key_ref) = @_;

    #Variables
    my $block_len = scalar(@$block_ref);
    my $key_len = scalar(@$key_ref);

    # Primeros 16 bits de la clave (Funciona igual con misma clave)
    AES_AddRoundKey($block_ref, [@$key_ref[0..15]]);

    my $i = 16;
    while ($i < $key_len - 16) {
        AES_SubBytes($block_ref, \@AES_Sbox);
        AES_ShiftRows($block_ref, \@AES_ShiftRowTab);
        AES_MixColumns($block_ref);
        # Segundos 16 bits de la clave (Funciona igual con misma clave)
        AES_AddRoundKey($block_ref, [@$key_ref[$i .. $i + 15]]);
        $i += 16;
    }

    AES_SubBytes($block_ref, \@AES_Sbox);
    AES_ShiftRows($block_ref, \@AES_ShiftRowTab);
    # Segundos 16 bits de la clave
    AES_AddRoundKey($block_ref, [@$key_ref[$i-1 .. $key_len - 1]]);

# EXPLICACIÓN:  AES-Advanced Encryption Standard
#   El algoritmo AES se caracteriza por:
#       -> Bloques de mensajes fijos de 128 bits --> Matrices de 4x4 (16bytes)
#       -> Clave Privada/Secreta de: 
#           --> 128 bits: 
#                   ---> 9 + 1 vueltas y subclaves (a partir de la principal)
#           --> 192 bits:
#                   ----> 11 + 1 vueltas y subclaves (a partir de la principal)
#           --> 256 bits:
#                   ---> 13 + 1 vueltas y subclaves (a partir de la principal)
#       -> Trabaja en el cuerpo GF(2^8) con polinomio irreducible (x^8+ x^4 + x^3 + x + 1)
#   Operaciones del AES (KS=256bits):
#       -> Inicio:
#               1) AddRoundKey / InvAddRoundKey: 
#                   - SecretKey XOR Message
#       -> 13 vueltas:
#               2) SubBytes / InvSubBytes:
#                   - Sustitución TODOS valores matriz por otros mediante tabla 33=C3, por ejemplo. 
#               3) ShiftRows / InvShiftRows:
#                   - 1ª fila = NO rota      => |C3|67|92|67| --> |C3|67|92|67|
#                   - 2ª fila = rota 1 byte  => |0C|00|CB|3B| --> |00|CB|3B|0C|
#                   - 3ª fila = rota 2 bytes => |D7|6B|4C|A0| --> |4C|A0|D7|6B|
#                   - 4ª fila = rota 3 bytes => |C9|6E|29|1A| --> |1A|C9|6E|29|
#               4) MixColumns / InvMixColumns:
#                   - Operaciones polinómicas en el campo de Galois GF(2^8) 
#                   - Polinómio fijo (cte) .* fila = MATRIZ DE ESTADO 
#                        |02|03|01|01|   |C3|   |CB|
#                        |01|02|03|01|   |00|   |0D|
#                        |01|01|02|03| x |4C| = |75| Con todas las columnas.
#                        |03|01|01|02|   |1A|   |26|
#               5) AddRoundKey / InvAddRoundKey
#                   - (Matriz de estado) XOR (KS 1ªvuelta) = Matriz de estado (vuelta 1)
#               6) Expanasión K (Creación nueva clave):
#                   6.1) RotWord = Rotación del 1er byte de la última columna (palabra 4 bytes):
#                           |63|65|20|62*|                  |69|
#                           |6C|20|31|69*|                  |74|
#                           |61|64|32|74*| => RotWord(*) => |73|
#                           |76|65|38|73*|                  |62|
#                   6.2) SubBytes a la última palabra:
#                           |69|    |F9|
#                           |74|    |92|
#                           |73| => |8F|
#                           |62|    |AA|
#                   6.3) XOR [i-3] (1ª palabra/columna):
#                           |F9|     |63|   |9A|
#                           |92|     |6C|   |FE|
#                           |8F| XOR |61| = |EE|
#                           |AA|     |76|   |DC|
#                   6.4) XOR RCON (vector conocido): 
#                           |01|02|04|08|10|20|40|80|1B|36|                      |01|     |9A|
#                           |00|00|00|00|00|00|00|00|00|00|                      |00|     |FE|
#                           |00|00|00|00|00|00|00|00|00|00| == Primera vuelta => |00| XOR |EE| 
#                           |00|00|00|00|00|00|00|00|00|00|                      |00|     |DC|
#       -> Vuelta 14:
#               6) SubBytes / InvSubBytes:
#               7) ShiftRows / InvShiftRows:
#               8) AddRoundKeyv / InvAddRoundKey
}

sub hexstr2array{
    #Argumentos:
    my ($input, $length) = @_;
    # Variables:
    my @output = ();
    for (my $i = 0; $i < $length; $i++) {
        if ($i < length($input) / 2) {
            $output[$i] = hex(substr($input, $i * 2, 2));
        } else {
            $output[$i] = 0;
        }
    }
    return @output;

# EXPLICACIÓN:
#   Paso de string HEXADECIMAL a array DECIMAL:
#       "54494641" --> |84|73|70|65|0|... hasta lenght ... |0|
}

sub str2hexstr {
    #Argumentos:
    my ($input) = @_;
    #Variables:
    my $output = "";
    for my $char (split //, $input) {
        $output .= sprintf("%02x", ord($char));
    }
    return $output;
# EXPLICACIÓN:
#   Paso de string a string HEXADECIMAL:
#       "ggx669Ju" --> 6767783636394a75
}

sub array2hexstr {
    #Argumentos:
    my ($input_ref) = @_;
    #Variables:
    my $output = "";
    for my $byte (@$input_ref) {
        $output .= sprintf("%02x", $byte);
    }
    return $output;
}

sub encode {
    #Argumentos:
    my ($password) = @_;
    #Variables
    my $key = "54494641";   # Hexadecimal: 8caract. * 4bits/caract. = 32bits
    my @private_key_byte = hexstr2array($key, 32);  # 54494641 --> |84|73|70|65|0|... 27 ...|0| (32) --> 32bytes*8bits/byte = 256 bits
    my @passwd_byte = hexstr2array(str2hexstr($password), 64);  # ggx669Ju --> |103|103|120|54|54|57|74|117|0|... 54 ...|0| (64) --> 64bytes*8bits/byte = 512bits
    my @output_byte;
    AES_Init();
    for (my $i = 0; $i < 4; $i++) {
        # Creación de 4 bloques fijos de 128 bits cada uno (4x4):
        my @block = @passwd_byte[$i * 16 .. ($i + 1) * 16 - 1]; # 4x4=16 --> 16bytes*8bits/bytes=128bits (0..15)
        AES_Encrypt(\@block, \@private_key_byte);
        push @output_byte, @block;
    }
    my $output = array2hexstr(\@output_byte);
    AES_Done();
    return $output;

#EXPLICACIÓN:
#Algoritmo de cifrado por bloques con clave privada AES - Advanced Encryption Standard
# M4x4(128 bits) --> AES --> C(128 bits)
#                     ^
#                     |
#                K(256 bits)  
#                      -> En este caso 256 bits
#
# Cadena: "contrasena AESLibrary
#   ---------
#   |c|t|s| |
#   |-+-+-+-|
#   |o|r|e|1|
#   |-+-+-+-|
#   |n|a|n|2|
#   |-+-+-+-|
#   |t|s|a|3|
#   ---------
}

1;  # Fin del paquete