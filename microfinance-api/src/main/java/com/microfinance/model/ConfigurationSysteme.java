package com.microfinance.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import lombok.Generated;

@Entity
@Table(
   name = "CONFIGURATION_SYSTEME"
)
public class ConfigurationSysteme {
   @Id
   @Column(
      name = "CLE_CONFIGURATION",
      length = 100
   )
   private String cleConfiguration;
   @Column(
      name = "VALEUR_CONFIGURATION",
      nullable = false,
      length = 255
   )
   private String valeurConfiguration;

   @Generated
   public static ConfigurationSystemeBuilder builder() {
      return new ConfigurationSystemeBuilder();
   }

   @Generated
   public String getCleConfiguration() {
      return this.cleConfiguration;
   }

   @Generated
   public String getValeurConfiguration() {
      return this.valeurConfiguration;
   }

   @Generated
   public void setCleConfiguration(final String cleConfiguration) {
      this.cleConfiguration = cleConfiguration;
   }

   @Generated
   public void setValeurConfiguration(final String valeurConfiguration) {
      this.valeurConfiguration = valeurConfiguration;
   }

   @Generated
   public ConfigurationSysteme() {
   }

   @Generated
   public ConfigurationSysteme(final String cleConfiguration, final String valeurConfiguration) {
      this.cleConfiguration = cleConfiguration;
      this.valeurConfiguration = valeurConfiguration;
   }

   @Generated
   public static class ConfigurationSystemeBuilder {
      @Generated
      private String cleConfiguration;
      @Generated
      private String valeurConfiguration;

      @Generated
      ConfigurationSystemeBuilder() {
      }

      @Generated
      public ConfigurationSystemeBuilder cleConfiguration(final String cleConfiguration) {
         this.cleConfiguration = cleConfiguration;
         return this;
      }

      @Generated
      public ConfigurationSystemeBuilder valeurConfiguration(final String valeurConfiguration) {
         this.valeurConfiguration = valeurConfiguration;
         return this;
      }

      @Generated
      public ConfigurationSysteme build() {
         return new ConfigurationSysteme(this.cleConfiguration, this.valeurConfiguration);
      }

      @Generated
      public String toString() {
         return "ConfigurationSysteme.ConfigurationSystemeBuilder(cleConfiguration=" + this.cleConfiguration + ", valeurConfiguration=" + this.valeurConfiguration + ")";
      }
   }
}
