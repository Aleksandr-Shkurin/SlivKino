FROM node:22-bookworm-slim

WORKDIR /app

# better-sqlite3 is a native Node.js module; these tools allow npm to compile it if needed.
RUN apt-get update \
    && apt-get install -y --no-install-recommends python3 make g++ \
    && rm -rf /var/lib/apt/lists/*

COPY package*.json ./
RUN npm install --omit=dev \
    && npm cache clean --force

COPY . .
RUN mkdir -p /data \
    && chown -R node:node /app /data

ENV NODE_ENV=production \
    PORT=3000 \
    DB_PATH=/data/slivkino.sqlite

USER node
EXPOSE 3000
CMD ["npm", "start"]
