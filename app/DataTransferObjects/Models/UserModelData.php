<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Models;

use Illuminate\Http\UploadedFile;

class UserModelData
{
    /**
     * The country ID.
     *
     * @var string|null
     */
    private string|null $country_id = null;

    /**
     * The first name.
     *
     * @var string|null
     */
    private string|null $first_name = null;

    /**
     * The last name.
     *
     * @var string|null
     */
    private string|null $last_name = null;

    /**
     * The email.
     *
     * @var string|null
     */
    private string|null $email = null;

    /**
     * The password.
     *
     * @var string|null
     */
    private string|null $password = null;

    /**
     * The username.
     *
     * @var string|null
     */
    private string|null $username = null;

    /**
     * The phone number.
     *
     * @var string|null
     */
    private string|null $phone_number = null;

    /**
     * The date of birth.
     *
     * @var string|null
     */
    private string|null $date_of_birth = null;

    /**
     * The FCM Token.
     *
     * @var string|null
     */
    private string|null $fcm_token = null;

    /**
     * The avatar.
     *
     * @var string|null
     */
    private UploadedFile|null $avatar = null;

    /**
     * The referee's code.
     *
     * @var string|null
     */
    private string|null $ref_code = null;

    private string|null $address = null;
    private string|null $zipcode = null;
    private string|null $ssn = null;
    private string|null $nationality = null;
    private string|null $experience = null;
    private bool|null $employed = null;
    private string|null $id_type = null;
    private string|null $id_number = null;
    private UploadedFile|null $front_id = null;
    private UploadedFile|null $back_id = null;
    private string|null $currecnyId = null;
    private string|null $stateId = null;
    private string|null $city = null;


    /**
     * Get the country ID.
     *
     * @return string|null
     */
    public function getCountryId(): string|null
    {
        return $this->country_id;
    }

    /**
     * Set the country ID.
     *
     * @param string|null $countryId The country ID.
     *
     * @return self
     */
    public function setCountryId(string|null $countryId): self
    {
        $this->country_id = $countryId;

        return $this;
    }

    /**
     * Get the first name.
     *
     * @return string|null
     */
    public function getFirstName(): string|null
    {
        return $this->first_name;
    }

    /**
     * Set the first name.
     *
     * @param string|null $firstName The first name.
     *
     * @return self
     */
    public function setFirstName(string|null $firstName): self
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get the last name.
     *
     * @return string|null
     */
    public function getLastName(): string|null
    {
        return $this->last_name;
    }

    /**
     * Set the last name.
     *
     * @param string|null $lastName The last name.
     *
     * @return self
     */
    public function setLastName(string|null $lastName): self
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get the email.
     *
     * @return string|null
     */
    public function getEmail(): string|null
    {
        return $this->email;
    }

    /**
     * Set the email.
     *
     * @param string|null $email The email.
     *
     * @return self
     */
    public function setEmail(string|null $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the phone number.
     *
     * @return string|null
     */
    public function getPhoneNumber(): string|null
    {
        return $this->phone_number;
    }

    /**
     * Set the phone number.
     *
     * @param string|null $phoneNumber The phone number.
     *
     * @return self
     */
    public function setPhoneNumber(string|null $phoneNumber): self
    {
        $this->phone_number = $phoneNumber ? str_replace(' ', '', $phoneNumber) : null;

        return $this;
    }

    /**
     * Get the avatar.
     *
     * @return \Illuminate\Http\UploadedFile|null
     */
    public function getAvatar(): UploadedFile|null
    {
        return $this->avatar;
    }

    /**
     * Set the avatar.
     *
     * @param \Illuminate\Http\UploadedFile|null $avatar The avatar.
     *
     * @return self
     */
    public function setAvatar(UploadedFile|null $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }


    /**
     * Get the password.
     *
     * @return string|null
     */
    public function getPassword(): string|null
    {
        return $this->password;
    }

    /**
     * Set the password.
     *
     * @param string|null $password The password.
     *
     * @return self
     */
    public function setPassword(string|null $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the username.
     *
     * @return string|null
     */
    public function getUsername(): string|null
    {
        return $this->username;
    }

    /**
     * Set the username.
     *
     * @param string|null $username The username.
     *
     * @return self
     */
    public function setUsername(string|null $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the date of birth.
     *
     * @return string|null
     */
    public function getDateOfBirth(): string|null
    {
        return $this->date_of_birth;
    }

    /**
     * Set the date of birth.
     *
     * @param string|null $dateOfBirth The date of birth.
     *
     * @return self
     */
    public function setDateOfBirth(string|null $dateOfBirth): self
    {
        $this->date_of_birth = $dateOfBirth;

        return $this;
    }

    /**
     * Get the FCM Token.
     *
     * @return string|null
     */
    public function getFcmToken(): string|null
    {
        return $this->fcm_token;
    }

    /**
     * Set the FCM Token.
     *
     * @param string|null $fcmToken The FCM Token.
     *
     * @return self
     */
    public function setFcmToken(string|null $fcmToken): self
    {
        $this->fcm_token = $fcmToken;

        return $this;
    }

    /**
     * Get the referee's code.
     *
     * @return string|null
     */
    public function getRefCode(): string|null
    {
        return $this->ref_code;
    }

    /**
     * Set the referee's code.
     *
     * @param string|null $refCode The referee's code.
     *
     * @return self
     */
    public function setRefCode(string|null $refCode): self
    {
        $this->ref_code = $refCode;

        return $this;
    }

    /**
     * Get the address.
     *
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Set the address.
     *
     * @param string|null $address The address.
     *
     * @return self
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get the zipcode.
     *
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * Set the zipcode.
     *
     * @param string|null $zipcode The zipcode.
     *
     * @return self
     */
    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    /**
     * Get the SSN.
     *
     * @return string|null
     */
    public function getSsn(): ?string
    {
        return $this->ssn;
    }

    /**
     * Set the SSN.
     *
     * @param string|null $ssn The SSN.
     *
     * @return self
     */
    public function setSsn(?string $ssn): self
    {
        $this->ssn = $ssn;
        return $this;
    }

    /**
     * Get the nationality.
     *
     * @return string|null
     */
    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * Set the nationality.
     *
     * @param string|null $nationality The nationality.
     *
     * @return self
     */
    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;
        return $this;
    }

    /**
     * Get the experience.
     *
     * @return string|null
     */
    public function getExperience(): ?string
    {
        return $this->experience;
    }

    /**
     * Set the experience.
     *
     * @param string|null $experience The experience.
     *
     * @return self
     */
    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;
        return $this;
    }

    /**
     * Get the employed status.
     *
     * @return bool|null
     */
    public function getEmployed(): ?bool
    {
        return $this->employed;
    }

    /**
     * Set the employed status.
     *
     * @param bool|null $employed The employed status.
     *
     * @return self
     */
    public function setEmployed(?bool $employed): self
    {
        $this->employed = $employed;
        return $this;
    }

    /**
     * Get the ID type.
     *
     * @return string|null
     */
    public function getIdtype(): ?string
    {
        return $this->id_type;
    }

    /**
     * Set the ID type.
     *
     * @param string|null $idNumber The ID number.
     *
     * @return self
     */
    public function setIdType(?string $idType): self
    {
        $this->id_type = $idType;
        return $this;
    }

    /**
     * Get the ID number.
     *
     * @return string|null
     */
    public function getIdNumber(): ?string
    {
        return $this->id_number;
    }

    /**
     * Set the ID number.
     *
     * @param string|null $idNumber The ID number.
     *
     * @return self
     */
    public function setIdNumber(?string $idNumber): self
    {
        $this->id_number = $idNumber;
        return $this;
    }

    /**
     * Get the frontId.
     *
     * @return \Illuminate\Http\UploadedFile|null
     */
    public function getFrontId(): UploadedFile|null
    {
        return $this->front_id;
    }

    /**
     * Set the frontId.
     *
     * @param \Illuminate\Http\UploadedFile|null $frontId The frontId.
     *
     * @return self
     */
    public function setFrontId(UploadedFile|null $frontId): self
    {
        $this->front_id = $frontId;

        return $this;
    }

    /**
     * Get the frontId.
     *
     * @return \Illuminate\Http\UploadedFile|null
     */
    public function getBackId(): UploadedFile|null
    {
        return $this->back_id;
    }

    /**
     * Set the backId.
     *
     * @param \Illuminate\Http\UploadedFile|null $backId The backId.
     *
     * @return self
     */
    public function setBackId(UploadedFile|null $backId): self
    {
        $this->back_id = $backId;

        return $this;
    }

    public function getCurrencyId(): ?string
    {
        return $this->currecnyId;
    }

    public function setCurrencyId(?string $currecnyId): self
    {
        $this->currecnyId = $currecnyId;
        return $this;
    }

    public function getStateId(): ?string
    {
        return $this->stateId;
    }

    public function setStateId(?string $stateId): self
    {
        $this->stateId = $stateId;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }
}
