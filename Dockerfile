FROM node:20-alpine
WORKDIR /app

# Instalar dependencias del sistema si necesitas build tools
RUN apk add --no-cache python3 make g++

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar dependencias
RUN npm ci

# Copiar el resto
COPY . .

# Exponer puerto
EXPOSE 5173

# Comando de inicio
CMD ["sh", "-c", "npm run dev -- --host 0.0.0.0"]