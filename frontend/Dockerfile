FROM node:14-slim

# Create an application directory
RUN mkdir -p /frontend

# The /app directory should act as the main application directory
WORKDIR /frontend

# Copy the app package.json and yarn.lock file
COPY ./package.json ./
COPY ./yarn.lock ./

# Copy or project directory (locally) in the current directory of our docker image (/frontend)
COPY . .

# Install node packages
RUN yarn install

# Build the app
RUN yarn build

# Expose $PORT on container.
# We use a varibale here as the port is something that can differ on the environment.
EXPOSE $PORT

# Set host to localhost / the docker image
ENV NUXT_HOST=0.0.0.0

# Set app port
ENV NUXT_PORT=$PORT

# Set the base url
ENV PROXY_API=$PROXY_API

# Set the browser base url
ENV PROXY_LOGIN=$PROXY_LOGIN

# Set the recaptcha
ENV ENABLE_RECAPTCHA=$ENABLE_RECAPTCHA

# Start the app
CMD [ "yarn", "dev" ]
