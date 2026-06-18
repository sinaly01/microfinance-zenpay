package com.microfinance.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "BLACK_LIST_JETONS"
)
public class BlackListJeton {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "jeton_seq"
   )
   @SequenceGenerator(
      name = "jeton_seq",
      sequenceName = "JETON_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_JETON_BLOQUE"
   )
   private Long idJetonBloque;
   @Column(
      name = "VALEUR_JETON",
      nullable = false,
      unique = true,
      length = 500
   )
   private String valeurJeton;
   @Column(
      name = "DATE_EXPIRATION",
      nullable = false
   )
   private LocalDateTime dateExpiration;
   @Column(
      name = "DATE_BLOCAGE",
      nullable = false
   )
   private LocalDateTime dateBlocage;

   @PrePersist
   public void prePersist() {
      this.dateBlocage = LocalDateTime.now();
   }

   @Generated
   public static BlackListJetonBuilder builder() {
      return new BlackListJetonBuilder();
   }

   @Generated
   public Long getIdJetonBloque() {
      return this.idJetonBloque;
   }

   @Generated
   public String getValeurJeton() {
      return this.valeurJeton;
   }

   @Generated
   public LocalDateTime getDateExpiration() {
      return this.dateExpiration;
   }

   @Generated
   public LocalDateTime getDateBlocage() {
      return this.dateBlocage;
   }

   @Generated
   public void setIdJetonBloque(final Long idJetonBloque) {
      this.idJetonBloque = idJetonBloque;
   }

   @Generated
   public void setValeurJeton(final String valeurJeton) {
      this.valeurJeton = valeurJeton;
   }

   @Generated
   public void setDateExpiration(final LocalDateTime dateExpiration) {
      this.dateExpiration = dateExpiration;
   }

   @Generated
   public void setDateBlocage(final LocalDateTime dateBlocage) {
      this.dateBlocage = dateBlocage;
   }

   @Generated
   public BlackListJeton() {
   }

   @Generated
   public BlackListJeton(final Long idJetonBloque, final String valeurJeton, final LocalDateTime dateExpiration, final LocalDateTime dateBlocage) {
      this.idJetonBloque = idJetonBloque;
      this.valeurJeton = valeurJeton;
      this.dateExpiration = dateExpiration;
      this.dateBlocage = dateBlocage;
   }

   @Generated
   public static class BlackListJetonBuilder {
      @Generated
      private Long idJetonBloque;
      @Generated
      private String valeurJeton;
      @Generated
      private LocalDateTime dateExpiration;
      @Generated
      private LocalDateTime dateBlocage;

      @Generated
      BlackListJetonBuilder() {
      }

      @Generated
      public BlackListJetonBuilder idJetonBloque(final Long idJetonBloque) {
         this.idJetonBloque = idJetonBloque;
         return this;
      }

      @Generated
      public BlackListJetonBuilder valeurJeton(final String valeurJeton) {
         this.valeurJeton = valeurJeton;
         return this;
      }

      @Generated
      public BlackListJetonBuilder dateExpiration(final LocalDateTime dateExpiration) {
         this.dateExpiration = dateExpiration;
         return this;
      }

      @Generated
      public BlackListJetonBuilder dateBlocage(final LocalDateTime dateBlocage) {
         this.dateBlocage = dateBlocage;
         return this;
      }

      @Generated
      public BlackListJeton build() {
         return new BlackListJeton(this.idJetonBloque, this.valeurJeton, this.dateExpiration, this.dateBlocage);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idJetonBloque;
         return "BlackListJeton.BlackListJetonBuilder(idJetonBloque=" + var10000 + ", valeurJeton=" + this.valeurJeton + ", dateExpiration=" + String.valueOf(this.dateExpiration) + ", dateBlocage=" + String.valueOf(this.dateBlocage) + ")";
      }
   }
}
