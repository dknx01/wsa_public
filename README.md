# Weltsozialamt

Install:
1. use the provided Docker container:
```bash
docker [-f docker-compose.prod.yaml] compose up -d
```
2. Install dependencies
```bash
docker exec -it [container name] bash -
composer install
./bin/console doc:mig:mig -n
./rebuild.sh or ./rebuild.prod.sh
``` 
3. Configure the app
   ```bash
   cp wsa.conf.php.dist wsa.conf.php
   vim wsa.conf.php
   ```
   Edit the title, impress and logo/images parameters. 
  
   Logos must be provided as svg file. 

   Helper images must be provided as png files.
