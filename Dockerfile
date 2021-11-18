FROM php:fpm-alpine3.14

# Labels
LABEL unilabot.maintainer="inutwp <inutwp.com>"
LABEL unilabot.version="v1.0"
LABEL unilabot.base.image="php:fpm-alpine3.14"

# Argument List
ARG user
ARG work_dir
ARG config_dir
ARG script_dir
ARG src_dir

# Install Packages
RUN apk --update --no-cache add \
	ca-certificates \
	bash \
	vim \
	tzdata \
    htop \
	supervisor \
	&& cp /usr/share/zoneinfo/Asia/Jakarta /etc/localtime \
	&& echo "Asia/Jakarta" > /etc/timezone \
	&& rm -rf /var/lib/apt/lists/* \
	&& rm -rf /var/cache/apk/*

# Configure php-fpm
COPY $config_dir/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Configure supervisord
COPY $config_dir/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create work dir
RUN mkdir -p $work_dir

# Configure cron
COPY $script_dir/start.sh $work_dir/start.sh

# Copy application
WORKDIR $work_dir
COPY --chown=$user $src_dir $work_dir
RUN chown -R $user:$user $work_dir \
	&& chmod -R 0644 $work_dir \
	&& find $work_dir -type d -print0 | xargs -0 chmod 0755 \
	&& chmod +x $work_dir/start.sh \
	&& chown -R $user:$user /run

# Switch to a non-root user  
USER $user

# Run supervisord
CMD ["/usr/bin/supervisord", "-n", "-c" ,"/etc/supervisor/conf.d/supervisord.conf"]