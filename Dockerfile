FROM php:7.4.25-fpm-alpine3.14

# Arguments
ARG user
ARG uid
ARG work_dir
ARG src_dir

# Install Requirement
RUN apk --update --no-cache add \
	ca-certificates \
	bash \
	vim \
	tzdata \
    htop \
	busybox-suid \
	supervisor \
# Set Timezone
	&& cp /usr/share/zoneinfo/Asia/Jakarta /etc/localtime \
	&& echo "Asia/Jakarta" > /etc/timezone \
# Remove Cache
	&& rm -rf /var/lib/apt/lists/* \
	&& rm -rf /var/cache/apk/*

# Set Config supervisord
COPY /supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR $work_dir

# Copy existing application directory
COPY $src_dir $work_dir

# Add cron
COPY /cron/cron.txt $work_dir/cron.txt
RUN /usr/bin/crontab $work_dir/cron.txt

# Add user
RUN addgroup -g $uid -S $user \
    && adduser -S -D -H -u $uid -h $work_dir -s /bin/bash -G $user -g $user $user \
# Change permission application directory
	&& chown -R $user:$user $work_dir \
	&& chmod -R 0644 $work_dir \
	&& find $work_dir -type d -print0 | xargs -0 chmod 0755

# Run supervisord
CMD ["/usr/bin/supervisord", "-n", "-c" ,"/etc/supervisor/conf.d/supervisord.conf"]

USER $user
