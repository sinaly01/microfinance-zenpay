package com.microfinance.config;

import com.microfinance.model.Client;
import com.microfinance.model.ConfigurationSysteme;
import com.microfinance.model.Gestionnaire;
import com.microfinance.model.OffreAbonnement;
import com.microfinance.model.enums.RoleUtilisateur;
import com.microfinance.model.enums.StatutKyc;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.ConfigurationSystemeRepository;
import com.microfinance.repository.GestionnaireRepository;
import com.microfinance.repository.OffreAbonnementRepository;
import java.math.BigDecimal;
import java.time.LocalDate;
import java.time.LocalDateTime;
import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.boot.CommandLineRunner;
import org.springframework.core.annotation.Order;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Component;
import org.springframework.transaction.annotation.Transactional;

@Component
@Order(1)
public class DataInitializer implements CommandLineRunner {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(DataInitializer.class);
   private final GestionnaireRepository gestionnaireRepository;
   private final ClientRepository clientRepository;
   private final OffreAbonnementRepository offreRepository;
   private final ConfigurationSystemeRepository configRepository;
   private final PasswordEncoder passwordEncoder;

   @Transactional
   public void run(String... args) {
      OffreAbonnement standard = this.creerOffreSiAbsente("STANDARD", BigDecimal.ZERO, new BigDecimal("1.50"), new BigDecimal("1.00"), false);
      OffreAbonnement offre1 = this.creerOffreSiAbsente("OFFRE_1", new BigDecimal("1000.00"), new BigDecimal("1.00"), new BigDecimal("1.00"), false);
      this.creerOffreSiAbsente("OFFRE_2", new BigDecimal("5000.00"), new BigDecimal("1.00"), BigDecimal.ZERO, true);
      if (!this.configRepository.existsById("STATUS_SYSTEME")) {
         this.configRepository.save(new ConfigurationSysteme("STATUS_SYSTEME", "OPERATIONNEL"));
         log.info("  ✓ Configuration système initialisée");
      }

      this.creerGestionnaireSiAbsent("superadmin@microfinance.local", "Super", "Admin", "SuperAdmin@2024", RoleUtilisateur.ROLE_SUPER_ADMIN);
      this.creerSuperAdminAvecCle("demo.admin@zenpay.local", "Admin", "Démo", "Demo@ZenPay2024", "ZenPay#Demo2024", RoleUtilisateur.ROLE_SUPER_ADMIN);
      this.creerGestionnaireSiAbsent("admin@microfinance.local", "Admin", "Système", "Admin@2024", RoleUtilisateur.ROLE_ADMIN_SYSTEME);
      this.creerGestionnaireSiAbsent("superviseur@microfinance.local", "Koné", "Fatou", "Admin@2024", RoleUtilisateur.ROLE_SUPERVISOR);
      this.creerGestionnaireSiAbsent("gestionnaire@microfinance.local", "Diallo", "Aminata", "Admin@2024", RoleUtilisateur.ROLE_GESTIONNAIRE);
      this.creerGestionnaireSiAbsent("adminbd@microfinance.local", "Koné", "Ibrahim", "Admin@2024", RoleUtilisateur.ROLE_ADMIN_BD);
      this.gestionnaireRepository.findByRole(RoleUtilisateur.ROLE_SUPER_ADMIN).forEach((g) -> {
         if (g.getCleSecrete() == null) {
            g.setCleSecrete(this.passwordEncoder.encode("SuperKey@2024"));
            this.gestionnaireRepository.save(g);
            log.info("  ✓ Clé secrète par défaut initialisée pour {}", g.getEmail());
         }

      });
      this.creerClientSiAbsent("client@microfinance.local", "Traoré", "Mariam", "Client@2024", standard, StatutKyc.VALIDE, "WAVE");
      this.creerClientSiAbsent("clientoffre1@microfinance.local", "Diop", "Seydou", "Client@2024", offre1, StatutKyc.VALIDE, "ORANGE");
   }

   private OffreAbonnement creerOffreSiAbsente(String nom, BigDecimal prix, BigDecimal fraisMomo, BigDecimal fraisVirement, boolean rib) {
      return (OffreAbonnement)this.offreRepository.findByNomOffre(nom).orElseGet(() -> {
         OffreAbonnement offre = OffreAbonnement.builder().nomOffre(nom).prixMensuel(prix).pourcentageFraisMomo(fraisMomo).fraisVirementInterne(fraisVirement).optionRibDispo(rib).build();
         log.info("  ✓ Offre '{}' créée", nom);
         return (OffreAbonnement)this.offreRepository.save(offre);
      });
   }

   private void creerSuperAdminAvecCle(String email, String nom, String prenom, String motDePasse, String cleSecrete, RoleUtilisateur role) {
      if (!this.gestionnaireRepository.existsByEmail(email)) {
         this.gestionnaireRepository.save(Gestionnaire.builder().email(email).nom(nom).prenom(prenom).motDePasse(this.passwordEncoder.encode(motDePasse)).cleSecrete(this.passwordEncoder.encode(cleSecrete)).role(role).dateEmbauche(LocalDate.now()).actif(true).build());
         log.info("  ✓ Super Admin démo '{}' créé", email);
      }

   }

   private void creerGestionnaireSiAbsent(String email, String nom, String prenom, String motDePasse, RoleUtilisateur role) {
      if (!this.gestionnaireRepository.existsByEmail(email)) {
         Gestionnaire.GestionnaireBuilder builder = Gestionnaire.builder().email(email).nom(nom).prenom(prenom).motDePasse(this.passwordEncoder.encode(motDePasse)).role(role).dateEmbauche(LocalDate.now()).actif(true);
         if (role == RoleUtilisateur.ROLE_SUPER_ADMIN) {
            builder.cleSecrete(this.passwordEncoder.encode("SuperKey@2024"));
         }

         this.gestionnaireRepository.save(builder.build());
         log.info("  ✓ Gestionnaire '{}' créé ({})", email, role);
      }

   }

   private void creerClientSiAbsent(String email, String nom, String prenom, String motDePasse, OffreAbonnement offre, StatutKyc kyc, String operateur) {
      if (!this.clientRepository.existsByEmail(email)) {
         ClientRepository var10000 = this.clientRepository;
         Client.ClientBuilder var10001 = Client.builder().email(email).nom(nom).prenom(prenom);
         int var10003 = Math.abs(email.hashCode());
         Client client = (Client)var10000.save(var10001.telephone("+2250" + (10000000 + var10003 % 89999999)).adresse("Cocody, Abidjan").numeroCni("CNI" + Math.abs(email.hashCode())).dateNaissance(LocalDate.of(1990, 1, 1)).motDePasse(this.passwordEncoder.encode(motDePasse)).role(RoleUtilisateur.ROLE_CLIENT).statutKyc(kyc).offreAbonnement(offre).operateurMomo(operateur).dateProchainPrelevement(LocalDateTime.now().plusMonths(1L)).actif(true).build());
         log.info("  ✓ Client test '{}' créé", email);
      } else {
         Client client = (Client)this.clientRepository.findByEmail(email).orElse((Object)null);
      }

   }

   @Generated
   public DataInitializer(final GestionnaireRepository gestionnaireRepository, final ClientRepository clientRepository, final OffreAbonnementRepository offreRepository, final ConfigurationSystemeRepository configRepository, final PasswordEncoder passwordEncoder) {
      this.gestionnaireRepository = gestionnaireRepository;
      this.clientRepository = clientRepository;
      this.offreRepository = offreRepository;
      this.configRepository = configRepository;
      this.passwordEncoder = passwordEncoder;
   }
}
