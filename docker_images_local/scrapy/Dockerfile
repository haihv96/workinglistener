FROM python:3

RUN pip install --no-cache-dir scrapy scrapyd
ARG UID=1000
ARG GID=1000
ARG USER=${USER}
RUN groupadd -g $GID -o $USER
RUN useradd -m -u $UID -g $GID -o -s /bin/bash $USER
USER $USER


CMD ["scrapyd"]
