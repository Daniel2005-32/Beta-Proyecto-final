FROM node:20-alpine
WORKDIR /app

# Instalar dependencias del sistema si necesitas build tools
RUN apk add --no-cache python3 make g++

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar dependencias
RUN npm ci

# Copiar el resto del código
COPY . .

# Render dinámicamente inyecta la variable $PORT. Por defecto Vite usa 5173, vamos a usar Vite preview para producción ligera o simplemente exponer el port.
# Lo mejor para Render de un Vue standalone es servir los estáticos o usar npm run dev dinámico.
ENV PORT=5173
EXPOSE $PORT

# Comando de inicio usando el puerto asignado por Render (o 5173)
CMD ["sh", "-c", "npm run dev -- --host 0.0.0.0 --port ${PORT}"]