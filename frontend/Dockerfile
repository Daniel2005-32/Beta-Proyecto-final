# Etapa de Construcción (Build Stage)
FROM node:20-alpine as build-stage

# Configurar directorio de trabajo
WORKDIR /app

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código y el entorno
COPY . .

# Compilar para producción (genera la carpeta dist/)
RUN npm run build

# Etapa de Producción con Nginx
FROM nginx:stable-alpine as production-stage

# Copiar configuración predeterminada de Nginx custom para SPA (Single Page Application)
COPY nginx.conf /etc/nginx/nginx.conf

# Copiar archivos generados por Vite desde la etapa anterior
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Exponer el puerto configurado dinámicamente por Render
EXPOSE 80

# Comando para iniciar Nginx
CMD ["nginx", "-g", "daemon off;"]
