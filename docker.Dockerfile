FROM laravelphp/vapor:php80

RUN apk --update add nodejs npm

RUN apk add --no-cache \
      nss \
      freetype \
      harfbuzz \
      ca-certificates \
      ttf-freefont

# de cacat. 
RUN apk add --no-cache chromium=86.0.4240.111-r0 --repository=http://dl-cdn.alpinelinux.org/alpine/v3.13/community

# Tell Puppeteer to skip installing Chrome. We'll be using the installed package.
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Puppeteer v10.2.0 works with Chromium 92.
RUN npm install -g puppeteer@10.2.0

COPY . /var/task
