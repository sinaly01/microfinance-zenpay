package com.microfinance.config;

import java.util.Properties;
import org.springframework.boot.autoconfigure.mail.MailProperties;
import org.springframework.boot.context.properties.EnableConfigurationProperties;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.mail.javamail.JavaMailSenderImpl;

@Configuration
@EnableConfigurationProperties({MailProperties.class})
public class MailConfig {
   @Bean
   public JavaMailSender mailSender(MailProperties props) {
      JavaMailSenderImpl sender = new JavaMailSenderImpl();
      String password = props.getPassword();
      if (password != null && !password.isBlank()) {
         sender.setHost(props.getHost());
         sender.setPort(props.getPort());
         sender.setUsername(props.getUsername());
         sender.setPassword(password);
         Properties p = sender.getJavaMailProperties();
         p.put("mail.smtp.auth", "true");
         p.put("mail.smtp.starttls.enable", "true");
         p.put("mail.transport.protocol", "smtp");
         return sender;
      } else {
         return sender;
      }
   }
}
