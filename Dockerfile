# Usa imagem oficial do PHP com Apache
FROM php:8.2-apache

# Copia os arquivos do projeto pro diretório padrão do Apache
COPY . /var/www/html/

# Exponha a porta usada pelo Apache
EXPOSE 80

# Inicia o Apache automaticamente
CMD ["apache2-foreground"]
