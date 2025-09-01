FROM node:22-alpine

WORKDIR /app

# Install deps first for better caching
COPY package*.json ./
RUN npm install

# Copy the rest
COPY . .

EXPOSE 5173
# docker-compose will override this with the dev command you set
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "5173"]